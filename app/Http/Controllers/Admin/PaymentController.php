<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PaymentController extends Controller
{
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'type' => 'required|in:dp,installment,full,offline',
            'method' => 'required|in:cash,transfer,midtrans,qris',
            'status' => 'required|in:pending,success',
            'notes' => 'nullable|string|max:255',
            'admin_password' => 'required|string',
        ]);

        [$authorized, $result] = $this->validateAdminPassword($request);
        if (!$authorized) {
            return $result;
        }
        /** @var \App\Models\User $user */
        $user = $result;

        DB::transaction(function () use ($payment, $request, $user) {
            $booking = $payment->booking()->lockForUpdate()->first();
            $originalStatus = $payment->status;

            $payment->update($request->only(['amount', 'type', 'method', 'status', 'notes']));

            $this->syncFinancialTransaction($payment, $user->id);

            if ($booking && ($originalStatus === 'success' || $payment->status === 'success')) {
                $this->refreshBookingTotals($booking);
            }
        });

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_password' => 'required|string',
        ]);

        [$authorized, $result] = $this->validateAdminPassword($request);
        if (!$authorized) {
            return $result;
        }

        DB::transaction(function () use ($payment) {
            $booking = $payment->booking()->lockForUpdate()->first();
            FinancialTransaction::where('reference', $payment->payment_code)->delete();

            $payment->delete();

            if ($booking) {
                $this->refreshBookingTotals($booking);
            }
        });

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Pembayaran berhasil dihapus.');
    }

    private function refreshBookingTotals($booking): void
    {
        if (!$booking) {
            return;
        }

        $totalPaid = $booking->payments()->where('status', 'success')->sum('amount');

        $paymentStatus = $this->determinePaymentStatus($booking, $totalPaid);
        $status = $booking->status;

        if ($status === 'pending' && $totalPaid > 0) {
            $status = 'dp_paid';
        } elseif ($status === 'dp_paid' && $totalPaid <= 0) {
            $status = 'pending';
        }

        $booking->update([
            'total_paid' => $totalPaid,
            'payment_status' => $paymentStatus,
            'status' => $status,
        ]);
    }

    private function determinePaymentStatus($booking, float $totalPaid): string
    {
        if ($totalPaid >= $booking->package_price) {
            return 'paid_full';
        }

        if ($totalPaid > 0) {
            return $booking->status === 'pending' ? 'dp_paid' : 'partially_paid';
        }

        return 'unpaid';
    }

    private function syncFinancialTransaction(Payment $payment, int $adminId): void
    {
        $query = FinancialTransaction::where('reference', $payment->payment_code);

        if ($payment->status !== 'success') {
            $query->delete();
            return;
        }

        $transactionData = [
            'booking_id' => $payment->booking_id,
            'created_by' => $adminId,
            'type' => 'income',
            'category' => $payment->method === 'cash' ? 'offline_payment' : 'manual_payment',
            'description' => 'Pembayaran ' . strtoupper($payment->type) . ' - ' . optional($payment->booking)->booking_code,
            'amount' => $payment->amount,
            'transaction_date' => optional($payment->paid_at)->toDateString() ?? now()->toDateString(),
            'reference' => $payment->payment_code,
        ];

        $existing = $query->first();
        if ($existing) {
            $existing->update($transactionData);
        } else {
            FinancialTransaction::create($transactionData);
        }
    }

    private function validateAdminPassword(Request $request): array
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return [false, $this->adminErrorResponse($request, 'Tidak memiliki akses admin.', 403)];
        }

        $throttleKey = sprintf('admin-password:%s:%s', $user->id, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.';
            return [false, $this->adminErrorResponse($request, $message, 429)];
        }

        if (!Hash::check($request->admin_password, $user->password)) {
            RateLimiter::hit($throttleKey, 300);
            return [false, $this->adminErrorResponse($request, 'Password admin salah.', 403)];
        }

        RateLimiter::clear($throttleKey);

        return [true, $user];
    }

    private function adminErrorResponse(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}
