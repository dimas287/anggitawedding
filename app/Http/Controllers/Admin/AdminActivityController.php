<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = $this->buildQuery($request)
            ->paginate(25)
            ->appends($request->query());
        $admins = User::where('role', 'admin')->orderBy('name')->get(['id', 'name']);

        if ($request->boolean('partial')) {
            return view('admin.activities.partials.table', compact('activities'));
        }

        return view('admin.activities.index', compact('activities', 'admins'));
    }

    public function export(Request $request)
    {
        $query = $this->buildQuery($request);
        $filename = 'riwayat-aktivitas-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'Admin', 'Aksi', 'Method', 'Route', 'URL', 'IP', 'User Agent', 'Payload']);

            $query->chunk(500, function ($activities) use ($handle) {
                foreach ($activities as $activity) {
                    fputcsv($handle, [
                        optional($activity->created_at)->format('Y-m-d H:i:s'),
                        optional($activity->user)->name ?? 'System',
                        $activity->action,
                        $activity->method,
                        $activity->route,
                        $activity->url,
                        $activity->ip_address,
                        Str::limit($activity->user_agent, 200, ''),
                        json_encode($activity->payload, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function buildQuery(Request $request)
    {
        $query = AdminActivity::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->method));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%")
                    ->orWhere('method', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }
}
