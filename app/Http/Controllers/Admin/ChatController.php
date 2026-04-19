<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function index(Request $request, Booking $booking)
    {
        $this->touchLastOnline(auth()->user(), $request);
        $booking->load('user');
        $chats = Chat::where('booking_id', $booking->id)
            ->with('sender', 'receiver')
            ->orderBy('created_at')
            ->get();
        Chat::where('booking_id', $booking->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return view('admin.chat', compact('booking', 'chats'));
    }

    public function send(Request $request, Booking $booking)
    {
        $this->touchLastOnline(auth()->user(), $request);
        $request->validate([
            'message' => 'required_without:attachment|string|nullable|max:2000',
            'is_internal' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,mp4,webm,mov|max:10240',
        ]);

        $chat = Chat::create([
            'booking_id' => $booking->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $booking->user_id,
            'message' => strip_tags($request->input('message', '')),
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if ($request->hasFile('attachment')) {
            $chat->update(['attachment' => $request->file('attachment')->store('chats', 'local')]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'chat' => $chat->fresh()->load('sender')]);
        }
        return back();
    }

    public function getMessages(Request $request, Booking $booking)
    {
        $this->touchLastOnline(auth()->user(), $request);
        $chats = Chat::where('booking_id', $booking->id)
            ->with('sender')
            ->orderBy('created_at')
            ->get();
        Chat::where('booking_id', $booking->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        $booking->loadMissing('user');
        return response()->json([
            'messages' => $chats->map(function($chat) {
                if ($chat->attachment) {
                    $chat->attachment_url = route('admin.chat.download', $chat->id);
                }
                return $chat;
            }),
            'typing' => $this->isTyping($booking, $booking->user_id),
            'presence' => [
                'client_online' => $this->isUserOnline($booking->user),
                'client_last_online' => $this->humanLastOnline($booking->user),
            ],
        ]);
    }

    public function downloadAttachment(Chat $chat)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if (!$chat->attachment) {
            abort(404);
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($chat->attachment);
    }

    public function typing(Request $request, Booking $booking)
    {
        $this->touchLastOnline(auth()->user(), $request);
        $request->validate(['is_typing' => 'required|boolean']);
        $this->setTyping($booking, auth()->id(), $request->boolean('is_typing'));
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

    public function allInbox()
    {
        $adminId = auth()->id();

        $bookings = Booking::select('bookings.*')
            ->whereHas('chats', function ($query) use ($adminId) {
                $query->where('is_internal', false)
                    ->where(function ($q) use ($adminId) {
                        $q->where('sender_id', $adminId)
                            ->orWhere('receiver_id', $adminId);
                    });
            })
            ->with([
                'user',
                'chats' => function ($query) use ($adminId) {
                    $query->where('is_internal', false)
                        ->where(function ($q) use ($adminId) {
                            $q->where('sender_id', $adminId)
                                ->orWhere('receiver_id', $adminId);
                        })
                        ->orderBy('created_at');
                },
            ])
            ->withCount(['chats as unread_count' => function ($query) use ($adminId) {
                $query->where('receiver_id', $adminId)
                    ->where('is_read', false)
                    ->where('is_internal', false);
            }])
            ->withMax(['chats as latest_chat_at' => function ($query) use ($adminId) {
                $query->where('is_internal', false)
                    ->where(function ($q) use ($adminId) {
                        $q->where('sender_id', $adminId)
                            ->orWhere('receiver_id', $adminId);
                    });
            }], 'created_at')
            ->orderByDesc('latest_chat_at')
            ->get();

        return view('admin.chat-inbox', compact('bookings'));
    }
}
