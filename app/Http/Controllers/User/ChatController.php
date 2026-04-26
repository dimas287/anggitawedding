<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function index(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $this->touchLastOnline(Auth::user(), $request);
        $admin = User::where('role', 'admin')->first();
        $chats = Chat::where('booking_id', $booking->id)
            ->where('is_internal', false)
            ->with('sender', 'receiver')
            ->orderBy('created_at')
            ->get();
        Chat::where('booking_id', $booking->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return view('user.chat', compact('booking', 'chats', 'admin'));
    }

    public function send(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $this->touchLastOnline(Auth::user(), $request);
        $request->validate([
            'message' => 'required_without:attachment|string|nullable|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,mp4,webm,mov|max:10240',
        ]);

        $admin = User::where('role', 'admin')->first();

        $chat = Chat::create([
            'booking_id' => $booking->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $admin?->id,
            'message' => strip_tags($request->message),
            'is_internal' => false,
        ]);

        if ($request->hasFile('attachment')) {
            $chat->update(['attachment' => $request->file('attachment')->store('chats', 'local')]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'chat' => $chat->fresh()->load('sender'),
            ]);
        }
        return back();
    }

    public function getMessages(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $this->touchLastOnline(Auth::user(), $request);
        $admin = User::where('role', 'admin')->first();
        $chats = Chat::where('booking_id', $booking->id)
            ->where('is_internal', false)
            ->with('sender')
            ->orderBy('created_at')
            ->get();
        Chat::where('booking_id', $booking->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json([
            'messages' => $chats->map(function($chat) {
                if ($chat->attachment) {
                    $chat->attachment_url = route('user.chat.download', $chat->id);
                }
                return $chat;
            }),
            'typing' => $this->isTyping($booking, $admin?->id),
            'presence' => [
                'admin_online' => $this->isUserOnline($admin),
                'admin_last_online' => $this->humanLastOnline($admin),
            ],
        ]);
    }

    public function downloadAttachment(Chat $chat)
    {
        $booking = $chat->booking;
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$chat->attachment) {
            abort(404);
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($chat->attachment);
    }

    public function typing(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $this->touchLastOnline(Auth::user(), $request);
        $request->validate(['is_typing' => 'required|boolean']);
        $this->setTyping($booking, Auth::id(), $request->boolean('is_typing'));
        return response()->json(['success' => true]);
    }

    private function typingCacheKey(Booking $booking, int $userId): string
    {
        return "chat:typing:{$booking->id}:{$userId}";
    }

    private function setTyping(Booking $booking, int $userId, bool $status): void
    {
        if ($status) {
            Cache::put($this->typingCacheKey($booking, $userId), true, now()->addSeconds(8));
        } else {
            Cache::forget($this->typingCacheKey($booking, $userId));
        }
    }

    private function isTyping(Booking $booking, ?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return Cache::has($this->typingCacheKey($booking, $userId));
    }

    private function touchLastOnline(?User $user, ?Request $request = null): void
    {
        if (!$user) {
            return;
        }

        if ($user->last_online_at && $user->last_online_at->gt(now()->subSeconds(30))) {
            return;
        }

        $user->forceFill([
            'last_online_at' => now(),
            'last_ip_address' => optional($request)->ip() ?? request()->ip(),
        ])->save();
    }

    private function isUserOnline(?User $user): bool
    {
        if (!$user || !$user->last_online_at) {
            return false;
        }

        return $user->last_online_at->gt(now()->subMinutes(2));
    }

    private function humanLastOnline(?User $user): ?string
    {
        if (!$user || !$user->last_online_at) {
            return null;
        }

        return $user->last_online_at->diffForHumans();
    }
}
