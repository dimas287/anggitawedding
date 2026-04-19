<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvitationMediaController extends Controller
{
    /**
     * Entry point for serving invitation media from local storage.
     */
    public function serve(Request $request, string $slug, string $type)
    {
        $invitation = Invitation::where('slug', $slug)->firstOrFail();

        // Security Check: Only allow if published OR if the user is the owner/admin
        if (!$invitation->is_published) {
            if (!auth()->check() || (auth()->id() !== $invitation->user_id && !auth()->user()->isAdmin())) {
                abort(403, 'Akses ditolak. Undangan belum dipublish.');
            }
        }

        $path = null;
        switch ($type) {
            case 'prewedding':
                $path = $invitation->photo_prewedding;
                break;
            case 'music':
                $path = $invitation->music_file;
                break;
            case 'groom':
                $path = $invitation->groom_photo;
                break;
            case 'bride':
                $path = $invitation->bride_photo;
                break;
            case 'gallery':
                $index = $request->query('i');
                $gallery = $invitation->gallery_photos ?? [];
                $path = $gallery[$index] ?? null;
                break;
            case 'dynamic':
                $slot = $request->query('slot');
                $fileIndex = $request->query('i');
                $mediaFiles = $invitation->media_files ?? [];
                $slotContent = $mediaFiles[$slot] ?? null;
                if (is_array($slotContent)) {
                    $path = $slotContent[$fileIndex] ?? null;
                } else {
                    $path = $slotContent;
                }
                break;
            case 'qris':
                $path = $invitation->qris_image;
                break;
        }

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'Media tidak ditemukan.');
        }

        return Storage::disk('local')->response($path);
    }
}
