<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                $this->flashAuthStatus('Akun Anda dinonaktifkan. Hubungi admin untuk aktivasi kembali.', 'error', 'Login Ditolak');
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan.']);
            }

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('info', 'Mohon verifikasi email Anda terlebih dahulu.');
            }

            if ($user->isAdmin()) {
                Auth::guard('admin')->login($user, $remember);
            } else {
                Auth::guard('admin')->logout();
            }

            $this->recordLoginActivity($user, $request);
            $this->flashAuthStatus('Selamat datang kembali, ' . ($user->name ?? 'klien') . '!', 'success', 'Login Berhasil');

            return $user->isAdmin()
                ? redirect()->intended(route('admin.dashboard'))
                : redirect()->intended(route('user.dashboard'));
        }

        $this->flashAuthStatus('Email atau password yang Anda masukkan tidak sesuai.', 'error', 'Login Gagal');
        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->only('email'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('user.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|min:5|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
        ]);

        $user->role = 'client';
        $user->is_active = true;
        $user->save();

        event(new Registered($user));
        Auth::login($user);
        $this->recordLoginActivity($user, $request);

        return redirect()->route('verification.notice')
            ->with('info', 'Kami telah mengirim tautan verifikasi ke email Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->flashAuthStatus('Anda telah logout dengan aman dari akun Anggita WO.', 'success', 'Logout Berhasil');
        return redirect()->route('landing');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                $user->role = 'client';
                $user->is_active = true;
                $user->save();

                event(new Registered($user));
            }

            if (!$user->is_active) {
                $this->flashAuthStatus('Akun Anda dinonaktifkan.', 'error', 'Login Ditolak');
                return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan.');
            }

            Auth::login($user, true);

            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return redirect()->route('verification.notice')
                    ->with('info', 'Kami telah mengirim tautan verifikasi ke email Google Anda.');
            }

            $this->recordLoginActivity($user, request());
            $this->flashAuthStatus('Selamat datang, ' . ($user->name ?? 'klien') . '!', 'success', 'Login Berhasil');

            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        } catch (\Exception $e) {
            $this->flashAuthStatus('Login Google gagal. Silakan coba lagi.', 'error', 'Login Gagal');
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }
    }

    // Password Reset Methods
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    private function recordLoginActivity(User $user, Request $request): void
    {
        $user->forceFill([
            'last_online_at' => now(),
            'last_ip_address' => $request->ip(),
        ])->save();
    }

    private function flashAuthStatus(string $message, string $type = 'success', ?string $headline = null): void
    {
        session()->flash('auth_status', $message);
        session()->flash('auth_status_type', $type);
        if ($headline) {
            session()->flash('auth_status_headline', $headline);
        }
    }
}
