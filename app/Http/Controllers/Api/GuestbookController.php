<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuestbookEntry;
use App\Models\Invitation;
use Illuminate\Http\Request;

class GuestbookController extends Controller
{
    public function index(string $slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $entries = GuestbookEntry::where('invitation_id', $invitation->id)
            ->latest()
            ->get(['id', 'name', 'message', 'created_at']);

        return response()->json($entries);
    }

    public function store(Request $request, string $slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $entry = GuestbookEntry::create([
            'invitation_id' => $invitation->id,
            'name'          => strip_tags($validated['name']),
            'message'       => strip_tags($validated['message']),
            'ip_address'    => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Ucapan berhasil dikirim!',
            'entry'   => [
                'id'         => $entry->id,
                'name'       => $entry->name,
                'message'    => $entry->message,
                'created_at' => $entry->created_at,
            ],
        ], 201);
    }
}
