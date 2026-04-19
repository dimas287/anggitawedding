<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\User;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::whereIn('status', ['dp_paid', 'in_progress'])->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'pending_consultations' => Consultation::where('status', 'pending')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'monthly_income' => FinancialTransaction::where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
            'monthly_expense' => FinancialTransaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
        ];
        $stats['monthly_profit'] = $stats['monthly_income'] - $stats['monthly_expense'];

        $recentBookings = Booking::with('user', 'package')->latest()->take(8)->get();
        $upcomingEvents = Booking::whereIn('status', ['dp_paid', 'in_progress'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(5)
            ->with('user', 'package')
            ->get();
        $pendingConsultations = Consultation::where('status', 'pending')
            ->orderBy('preferred_date')
            ->take(5)
            ->with('user')
            ->get();

        $monthlyData = FinancialTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(CASE WHEN type="income" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type="expense" THEN amount ELSE 0 END) as expense')
            )
            ->whereYear('transaction_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentBookings', 'upcomingEvents', 'pendingConsultations', 'monthlyData'
        ));
    }
}
