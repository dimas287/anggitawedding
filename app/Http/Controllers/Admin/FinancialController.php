<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        [$year, $month] = $this->resolvePeriod($request);

        $query = FinancialTransaction::with('booking', 'creator')
            ->whereYear('transaction_date', $year);
        if ($month !== 'all') $query->whereMonth('transaction_date', $month);
        if ($request->type) $query->where('type', $request->type);
        if ($request->category) $query->where('category', $request->category);
        if ($request->booking_id) $query->where('booking_id', $request->booking_id);

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $summary = [
            'total_income' => FinancialTransaction::where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->when($month !== 'all', fn($q) => $q->whereMonth('transaction_date', $month))
                ->sum('amount'),
            'total_expense' => FinancialTransaction::where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->when($month !== 'all', fn($q) => $q->whereMonth('transaction_date', $month))
                ->sum('amount'),
        ];
        $summary['profit'] = $summary['total_income'] - $summary['total_expense'];

        $monthlyChart = FinancialTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(CASE WHEN type="income" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type="expense" THEN amount ELSE 0 END) as expense')
            )
            ->whereYear('transaction_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $categoryBreakdown = FinancialTransaction::select('category', 'type', DB::raw('SUM(amount) as total'))
            ->whereYear('transaction_date', $year)
            ->when($month !== 'all', fn($q) => $q->whereMonth('transaction_date', $month))
            ->groupBy('category', 'type')
            ->get();

        $categories = FinancialTransaction::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.financial.index', compact(
            'transactions', 'summary', 'monthlyChart', 'categoryBreakdown', 'categories', 'year', 'month'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'booking_id' => 'nullable|exists:bookings,id',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['type', 'category', 'description', 'amount', 'transaction_date', 'booking_id', 'reference', 'notes']);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('financial', 'public');
        }

        FinancialTransaction::create($data);
        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function destroy(FinancialTransaction $financialTransaction)
    {
        $financialTransaction->delete();
        return back()->with('success', 'Transaksi dihapus.');
    }

    public function bookingFinancial(Booking $booking)
    {
        $booking->load('financialTransactions.creator', 'vendors', 'payments');
        $income = $booking->financialTransactions->where('type', 'income')->sum('amount');
        $expense = $booking->financialTransactions->where('type', 'expense')->sum('amount');
        $vendorCost = $booking->vendors->sum('cost');
        return view('admin.financial.booking', compact('booking', 'income', 'expense', 'vendorCost'));
    }

    public function exportPdf(Request $request)
    {
        [$year, $month] = $this->resolvePeriod($request);

        $transactions = FinancialTransaction::with('booking', 'creator')
            ->whereYear('transaction_date', $year)
            ->when($month !== 'all', fn($q) => $q->whereMonth('transaction_date', $month))
            ->orderBy('transaction_date')
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;

        // Build period string for PDF
        $period = $month === 'all'
            ? $year
            : sprintf('%s - %s', $year, date('F', mktime(0, 0, 0, (int) $month, 1)));

        // Summary array for PDF
        $summary = [
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'profit' => $profit,
        ];

        $pdf = Pdf::loadView('pdf.financial-report', compact('transactions', 'totalIncome', 'totalExpense', 'year', 'month', 'period', 'summary'));
        $filename = 'laporan-keuangan-' . $year . ($month !== 'all' ? '-' . str_pad($month, 2, '0', STR_PAD_LEFT) : '') . '.pdf';
        return $pdf->download($filename);
    }

    private function resolvePeriod(Request $request): array
    {
        $rawYear = $request->input('year');
        $rawMonth = $request->input('month');

        if ($rawMonth && str_contains($rawMonth, '-')) {
            [$parsedYear, $parsedMonth] = explode('-', $rawMonth, 2);
            $rawYear = $rawYear ?? $parsedYear;
            $rawMonth = $parsedMonth;
        }

        $year = is_numeric($rawYear) ? (int) $rawYear : now()->year;
        $month = $rawMonth === 'all'
            ? 'all'
            : (is_numeric($rawMonth) ? (int) $rawMonth : now()->month);

        return [$year, $month];
    }
}
