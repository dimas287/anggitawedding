<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioImage;
use App\Models\PortfolioMediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PortfolioMediaController extends Controller
{
    public function store(Request $request, PortfolioImage $portfolioImage)
    {
        $data = $request->validate([
            'type' => 'required|in:image_upload,video_upload,video_url',
            'media_upload' => 'nullable|file|max:51200', // 50MB
            'video_url' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $mediaType = in_array($data['type'], ['video_upload', 'video_url']) ? 'video' : 'image';
        $mediaPath = null;
        $videoUrl = null;

        if ($data['type'] === 'image_upload') {
            if (!$request->hasFile('media_upload')) {
                throw ValidationException::withMessages(['media_upload' => 'Unggah file gambar.']);
            }
            $request->validate(['media_upload' => 'image|mimes:jpg,jpeg,png,webp|max:5120']);
            $mediaPath = $request->file('media_upload')->store('portfolio-media', 'public');
        } elseif ($data['type'] === 'video_upload') {
            if (!$request->hasFile('media_upload')) {
                throw ValidationException::withMessages(['media_upload' => 'Unggah file video.']);
            }
            $request->validate(['media_upload' => 'mimetypes:video/mp4,video/webm|max:51200']);
            $mediaPath = $request->file('media_upload')->store('portfolio-media', 'public');
        } else { // video_url
            if (empty($data['video_url'])) {
                throw ValidationException::withMessages(['video_url' => 'Masukkan URL video.']);
            }
            $videoUrl = $data['video_url'];
        }

        $sortOrder = $data['sort_order'] ?? (($portfolioImage->mediaItems()->max('sort_order') ?? -1) + 1);

        $portfolioImage->mediaItems()->create([
            'media_type' => $mediaType,
            'media_path' => $mediaPath,
            'video_url' => $videoUrl,
            'sort_order' => $sortOrder,
        ]);

        return back()->with('success', 'Media tambahan berhasil ditambahkan.');
    }

    public function destroy(PortfolioImage $portfolioImage, PortfolioMediaItem $media)
    {
        abort_unless($media->portfolio_image_id === $portfolioImage->id, 404);

        if ($media->media_path) {
            Storage::disk('public')->delete($media->media_path);
        }

        $media->delete();

        return back()->with('success', 'Media berhasil dihapus.');
    }
}
