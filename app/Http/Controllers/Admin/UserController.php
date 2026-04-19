<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', 'client');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->select('users.*')
            ->addSelect([
                'last_online_at' => DB::table('sessions as s')
                    ->select('s.last_activity')
                    ->whereColumn('s.user_id', 'users.id')
                    ->orderByDesc('s.last_activity')
                    ->limit(1),
                'last_ip_address' => DB::table('sessions as s')
                    ->select('s.ip_address')
                    ->whereColumn('s.user_id', 'users.id')
                    ->orderByDesc('s.last_activity')
                    ->limit(1),
            ])
            ->withCount(['bookings as active_bookings_count' => function ($q) {
                $q->whereIn('status', ['pending', 'dp_paid', 'in_progress']);
            }, 'bookings'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('clients'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'client', 404);

        $bookings = Booking::with('package')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $consultations = Consultation::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'completed' => $user->bookings()->where('status', 'completed')->count(),
            'active' => $user->bookings()->whereIn('status', ['pending', 'dp_paid', 'in_progress'])->count(),
        ];

        return view('admin.users.show', compact('user', 'bookings', 'stats', 'consultations'));
    }
}
