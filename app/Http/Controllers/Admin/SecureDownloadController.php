<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingVendor;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SecureDownloadController extends Controller
{
    /**
     * Serve vendor payment proofs (Admin only).
     */
    public function downloadVendorProof(BookingVendor $bookingVendor)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        
        if (!$bookingVendor->proof_attachment || !Storage::disk('local')->exists($bookingVendor->proof_attachment)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download($bookingVendor->proof_attachment);
    }

    /**
     * Serve manual payment proofs (Admin and Owner only).
     */
    public function downloadPaymentProof(Payment $payment)
    {
        // Allow if admin OR if the payment belongs to the current user
        if (!Auth::user()->isAdmin() && $payment->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$payment->proof_attachment || !Storage::disk('local')->exists($payment->proof_attachment)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download($payment->proof_attachment);
    }
}
