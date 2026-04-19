<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FinancialTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function eventReport(Booking $booking)
    {
        $booking->load(
            'user',
            'package',
            'payments',
            'vendors',
            'rundowns',
            'financialTransactions',
            'invitation.template',
            'extraCharges',
            'fittings',
            'wardrobeItems',
            'documents',
            'review'
        );
        $income = $booking->financialTransactions->where('type', 'income')->sum('amount');
        $expense = $booking->financialTransactions->where('type', 'expense')->sum('amount');
        $pdf = Pdf::loadView('pdf.event-report', compact('booking', 'income', 'expense'))->setPaper('a4');
        return $pdf->download('laporan-event-' . $booking->booking_code . '.pdf');
    }

    public function invoicePdf(Booking $booking)
    {
        $booking->load('user', 'package', 'payments');
        $pdf = Pdf::loadView('pdf.invoice', compact('booking'))->setPaper('a4');
        return $pdf->download('invoice-' . $booking->booking_code . '.pdf');
    }
}
