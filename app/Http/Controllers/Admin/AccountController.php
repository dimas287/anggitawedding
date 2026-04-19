<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordChangeVerificationMail;
use App\Models\AdminActivity;
use App\Models\PasswordChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function settings()
    {
        $user = Auth::user();
        return view('admin.settings', compact('user'));
    }

    public function requestPasswordChange(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        PasswordChangeRequest::where('user_id', $user->id)->delete();

        $token = Str::random(64);
        $requestRecord = PasswordChangeRequest::create([
            'user_id' => $user->id,
            'token' => $token,
            'password_hash' => Hash::make($data['password']),
            'expires_at' => now()->addMinutes(30),
        ]);

        try {
            Mail::to($user->email)->send(new AdminPasswordChangeVerificationMail($user, $requestRecord));
        } catch (\Exception $e) {
            $requestRecord->delete();
            return back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }

        return back()->with('success', 'Link verifikasi sudah dikirim ke email admin. Silakan cek inbox.');
    }

    public function confirmPasswordChange(Request $request, string $token)
    {
        $requestRecord = PasswordChangeRequest::where('token', $token)->first();

        if (!$requestRecord || $requestRecord->expires_at->isPast()) {
            if ($requestRecord) {
                $requestRecord->delete();
            }
            return redirect()->route('login')->with('error', 'Link verifikasi sudah tidak berlaku. Silakan kirim ulang.');
        }

        $user = $requestRecord->user;
        if (!$user || !$user->isAdmin()) {
            $requestRecord->delete();
            return redirect()->route('login')->with('error', 'Link verifikasi tidak valid.');
        }

        if (!$request->filled('email') || !hash_equals(strtolower($user->email), strtolower((string) $request->query('email')))) {
            return redirect()->route('login')->with('error', 'Link verifikasi tidak valid.');
        }

        $user->update(['password' => $requestRecord->password_hash]);
        $requestRecord->delete();

        try {
            AdminActivity::create([
                'user_id' => $user->id,
                'action' => 'admin.password.changed',
                'method' => $request->getMethod(),
                'route' => optional($request->route())->getName(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'payload' => ['type' => 'password_change_confirmed'],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
        }

        return redirect()->route('login')->with('success', 'Password admin berhasil diubah. Silakan login kembali.');
    }
}
