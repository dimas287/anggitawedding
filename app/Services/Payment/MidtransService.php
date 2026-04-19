<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    public function generateSnapToken(Payment $payment, Booking $booking, array $meta): ?array
    {
        $enabledPayments = [
            'credit_card', 'bca_va', 'echannel', 'bni_va', 'bri_va', 
            'permata_va', 'other_va', 'alfamart', 'indomaret'
        ];

        if ($payment->amount <= 500000) {
            $enabledPayments = array_merge($enabledPayments, ['gopay', 'shopeepay', 'other_qris']);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $payment->payment_code,
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $payment->user->name,
                'email' => $payment->user->email,
                'phone' => $payment->user->phone ?? $booking->phone,
            ],
            'item_details' => [
                [
                    'id' => $meta['item_id'],
                    'price' => $payment->amount,
                    'quantity' => 1,
                    'name' => $meta['item_name'],
                ],
            ],
            'enabled_payments' => $enabledPayments,
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return [
                'snap_token' => $snapToken,
                'payment_code' => $payment->payment_code
            ];
        } catch (\Exception $e) {
            Log::error('Failed generating Midtrans snap token', [
                'booking_id' => $booking->id,
                'user_id' => $payment->user_id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function checkStatus(string $paymentCode): array
    {
        try {
            $status = \Midtrans\Transaction::status($paymentCode);
            return json_decode(json_encode($status), true);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function isValidSignature(Request $request): bool
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return false;
        }

        $serverKey = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }
}
