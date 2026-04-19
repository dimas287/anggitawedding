<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invitation;
use App\Models\FinancialTransaction;
use App\Mail\PaymentSuccessMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingFulfillmentService
{
    public function isInvitationOnlyOrder(Booking $booking): bool
    {
        $booking->loadMissing('package', 'invitation');

        if (!is_null($booking->is_invitation_only)) {
            return (bool) $booking->is_invitation_only;
        }

        $hasInvitation = (bool) $booking->invitation;
        $packageHasInvitation = (bool) (optional($booking->package)->has_digital_invitation ?? false);

        return $hasInvitation && !$packageHasInvitation;
    }

    public function getPaymentMeta(Booking $booking): array
    {
        $isInvitationOnly = $this->isInvitationOnlyOrder($booking);

        if ($isInvitationOnly) {
            $booking->loadMissing('invitation.template');
            $templateName = optional(optional($booking->invitation)->template)->name;
            $label = $templateName ? ('Undangan Digital - ' . $templateName) : 'Undangan Digital';

            return [
                'amount' => (int) round((float) $booking->package_price),
                'type' => 'full',
                'item_id' => 'INV-' . $booking->id,
                'item_name' => $label,
                'title' => 'Pembayaran Undangan',
            ];
        }

        return [
            'amount' => (int) $booking->dp_amount,
            'type' => 'dp',
            'item_id' => (string) $booking->package->id,
            'item_name' => 'DP 30% - ' . $booking->package->name,
            'title' => 'Pembayaran DP',
        ];
    }

    public function markPaymentSuccess(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);

            $booking = $payment->booking;
            $newTotalPaid = $booking->total_paid + $payment->amount;
            $isFullyPaid = $newTotalPaid >= $booking->package_price;

            $isInvitationOnly = $this->isInvitationOnlyOrder($booking);

            if (($isInvitationOnly || $payment->type === 'full') && !$isFullyPaid) {
                $booking->total_paid = $newTotalPaid;
                $booking->payment_status = 'partially_paid';
                $booking->status = 'pending';
                $booking->save();

                return;
            }

            $booking->status = 'dp_paid';
            $booking->payment_status = ($isInvitationOnly || $payment->type === 'full')
                ? 'paid_full'
                : ($isFullyPaid ? 'paid_full' : 'dp_paid');
            $booking->total_paid = $newTotalPaid;
            $booking->save();

            $ftCategory = ($isInvitationOnly || $payment->type === 'full') ? 'invitation_payment' : 'dp_payment';
            $ftDesc = ($isInvitationOnly || $payment->type === 'full')
                ? ('Pembayaran Undangan ' . $booking->booking_code)
                : ('DP Booking ' . $booking->booking_code);

            FinancialTransaction::create([
                'booking_id' => $booking->id,
                'created_by' => $booking->user_id,
                'type' => 'income',
                'category' => $ftCategory,
                'description' => $ftDesc,
                'amount' => $payment->amount,
                'transaction_date' => now()->toDateString(),
                'reference' => $payment->payment_code,
            ]);

            $packageAllowsInvitation = optional($booking->package)->has_digital_invitation ?? true;

            if ($packageAllowsInvitation && !$booking->invitation) {
                Invitation::create([
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'slug' => Str::slug($booking->groom_name . '-' . $booking->bride_name . '-' . $booking->id),
                    'groom_name' => $booking->groom_name,
                    'bride_name' => $booking->bride_name,
                    'reception_datetime' => $booking->event_date,
                    'reception_venue' => $booking->venue,
                    'reception_address' => $booking->venue_address,
                    'is_published' => false,
                    'rsvp_enabled' => true,
                ]);
            }
        });

        $this->dispatchSuccessEmail($payment);
    }

    private function dispatchSuccessEmail(Payment $payment): void
    {
        $booking = $payment->fresh()->booking;
        try {
            Mail::to($booking->user->email)->send(new PaymentSuccessMail($booking, $payment));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email konfirmasi pembayaran', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'user_email' => $booking->user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
