<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Mail\ManualPaymentUploadedMail;
use App\Services\Payment\MidtransService;
use App\Services\Booking\BookingFulfillmentService;
use App\Http\Requests\StoreManualPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    private MidtransService $midtransService;
    private BookingFulfillmentService $fulfillmentService;

    public function __construct(MidtransService $midtransService, BookingFulfillmentService $fulfillmentService)
    {
        $this->midtransService = $midtransService;
        $this->fulfillmentService = $fulfillmentService;
    }

    private function adminRecipients(): array
    {
        $settings = SiteSetting::getJson('consultation_settings', []);
        $rawEmails = $settings['admin_email'] ?? config('mail.from.address');

        $emails = collect(explode(',', (string) $rawEmails))
            ->map(fn ($email) => trim($email))
            ->filter(fn ($email) => !empty($email));

        if ($emails->isEmpty() && config('mail.from.address')) {
            $emails->push(config('mail.from.address'));
        }

        return $emails->unique()->all();
    }

    public function checkout(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        if ($booking->payment_status !== 'unpaid') {
            return redirect()->route('user.booking.show', $booking->id);
        }

        $meta = $this->fulfillmentService->getPaymentMeta($booking);
        $paymentTitle = $meta['title'];
        $payAmount = $meta['amount'];
        $isInvitationOnly = $this->fulfillmentService->isInvitationOnlyOrder($booking);

        return view('payment.checkout', compact('booking', 'paymentTitle', 'payAmount', 'isInvitationOnly'));
    }

    public function process(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $meta = $this->fulfillmentService->getPaymentMeta($booking);

        $existingPayment = Payment::where('booking_id', $booking->id)
            ->where('user_id', Auth::id())
            ->where('type', $meta['type'])
            ->where('method', 'midtrans')
            ->where('status', 'pending')
            ->first();

        // 1. Force New Session check vs Reuse
        if ($existingPayment && (int)$existingPayment->amount === (int)$meta['amount'] && !$request->has('reset')) {
            $response = $existingPayment->payment_response;
            $token = $response['snap_token'] ?? null;
            $isFresh = $existingPayment->updated_at->diffInMinutes(now()) < 50;

            if ($token && $isFresh) {
                return response()->json([
                    'snap_token' => $token,
                    'payment_code' => $existingPayment->payment_code
                ]);
            }
        }

        if ($existingPayment) {
            $existingPayment->update(['status' => 'failed']);
        }

        $paymentCode = Payment::generateCode();
        
        $payment = Payment::create([
            'payment_code' => $paymentCode,
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'amount' => $meta['amount'],
            'type' => $meta['type'],
            'method' => 'midtrans',
            'status' => 'pending',
            'transaction_id' => $paymentCode,
        ]);

        $snapData = $this->midtransService->generateSnapToken($payment, $booking, $meta);

        if ($snapData) {
            $payment->update(['payment_response' => ['snap_token' => $snapData['snap_token']]]);
            return response()->json($snapData);
        }

        return response()->json(['error' => 'Gagal membuat transaksi pembayaran. Silakan coba lagi.'], 500);
    }

    public function manual(StoreManualPaymentRequest $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        if ($booking->payment_status !== 'unpaid') {
            return redirect()->route('user.booking.show', $booking->id);
        }

        $meta = $this->fulfillmentService->getPaymentMeta($booking);
        $validated = $request->validated();
        $path = $request->file('proof_attachment')->store('payments/proofs', 'local');

        $isInvitationOnly = $this->fulfillmentService->isInvitationOnlyOrder($booking);
        $amount = $isInvitationOnly
            ? (float) $meta['amount']
            : (float) ($validated['amount'] ?? $meta['amount']);

        $payment = Payment::create([
            'payment_code' => Payment::generateCode(),
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'amount' => $amount,
            'type' => $meta['type'],
            'method' => $validated['method'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'proof_attachment' => $path,
        ]);

        $recipients = $this->adminRecipients();
        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new ManualPaymentUploadedMail($booking, $payment));
            } catch (\Exception $e) {
                Log::warning('Failed sending manual payment notification', [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('user.booking.show', $booking->id)
            ->with('success', 'Bukti pembayaran berhasil diupload. Silakan tunggu admin memverifikasi.');
    }

    public function notification(Request $request)
    {
        if (!$this->midtransService->isValidSignature($request)) {
            Log::warning('Midtrans webhook rejected: invalid signature', [
                'order_id' => $request->input('order_id'),
                'status_code' => $request->input('status_code'),
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->input('transaction_status');
        $orderId = $request->input('order_id');
        $fraudStatus = $request->input('fraud_status');

        $payment = Payment::where('payment_code', $orderId)->first();
        if (!$payment) {
            Log::warning('Midtrans webhook payment not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'challenge') {
                $payment->update(['status' => 'pending']);
            } else {
                $this->fulfillmentService->markPaymentSuccess($payment);
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $payment->update(['status' => 'failed']);
        } elseif ($transactionStatus === 'pending') {
            $payment->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'OK']);
    }

    public function success(Request $request, Booking $booking)
    {
        $meta = $this->fulfillmentService->getPaymentMeta($booking);

        $payment = Payment::where('booking_id', $booking->id)
            ->where('user_id', Auth::id())
            ->where('type', $meta['type'])
            ->latest()
            ->first();

        if ($payment && $payment->status === 'success') {
            return redirect()->route('user.booking.show', $booking->id)
                ->with('success', $meta['type'] === 'full'
                    ? 'Pembayaran undangan berhasil! Anda sekarang bisa publish undangan.'
                    : 'Pembayaran DP berhasil! Selamat datang sebagai klien resmi kami.');
        }

        $paymentDetails = [];
        if ($payment && $payment->status === 'pending' && $payment->method === 'midtrans') {
            try {
                $statusData = $this->midtransService->checkStatus($payment->payment_code);
                $currentResponse = $payment->payment_response ?? [];
                
                $mergedResponse = array_merge($currentResponse, $statusData);
                $payment->update([
                    'payment_response' => $mergedResponse
                ]);
                $paymentDetails = $mergedResponse;
            } catch (\Exception $e) {
                $paymentDetails = $payment->payment_response ?? [];
            }
        } else if ($payment) {
            $paymentDetails = $payment->payment_response ?? [];
        }

        $paymentStatus = $payment->status ?? 'pending';

        return view('payment.success', compact('booking', 'payment', 'paymentStatus', 'paymentDetails'));
    }
}
