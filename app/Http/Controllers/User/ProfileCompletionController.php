<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileCompletionController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('user.complete-profile', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string|min:5|max:255',
        ]);

        $user->update($data);

        return redirect()->intended(route('user.dashboard'))
            ->with('success', 'Profil berhasil dilengkapi. Terima kasih!');
    }
}
