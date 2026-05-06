<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstagramPostController extends Controller
{
    public function index()
    {
        $posts = InstagramPost::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return view('admin.instagram.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instagram_url' => 'required|url',
            'caption' => 'nullable|string',
            'media_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $data = $validated;
        
        if ($request->hasFile('media_upload')) {
            $data['media_path'] = $request->file('media_upload')->store('instagram', 'public');
        }

        InstagramPost::create($data);

        return redirect()->route('instagram-posts.index')->with('success', 'Postingan Instagram berhasil ditambahkan.');
    }

    public function update(Request $request, InstagramPost $instagramPost)
    {
        $validated = $request->validate([
            'instagram_url' => 'required|url',
            'caption' => 'nullable|string',
            'media_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $data = $validated;

        if ($request->hasFile('media_upload')) {
            if ($instagramPost->media_path) {
                Storage::disk('public')->delete($instagramPost->media_path);
            }
            $data['media_path'] = $request->file('media_upload')->store('instagram', 'public');
        }

        $instagramPost->update($data);

        return redirect()->route('instagram-posts.index')->with('success', 'Postingan Instagram berhasil diperbarui.');
    }

    public function destroy(InstagramPost $instagramPost)
    {
        if ($instagramPost->media_path) {
            Storage::disk('public')->delete($instagramPost->media_path);
        }
        $instagramPost->delete();

        return redirect()->route('instagram-posts.index')->with('success', 'Postingan Instagram berhasil dihapus.');
    }

    /**
     * Attempt to fetch metadata from Instagram URL
     * This is a "best effort" approach and may fail due to IG blocks.
     */
    public function fetchMetadata(Request $request)
    {
        $url = $request->url;
        
        try {
            // We use a simple GET request with a generic user agent
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengakses Instagram. Silakan upload gambar secara manual.']);
            }

            $html = $response->body();
            
            // Extract og:image
            preg_match('/property="og:image" content="([^"]+)"/', $html, $imageMatches);
            // Extract og:description (caption)
            preg_match('/property="og:description" content="([^"]+)"/', $html, $descMatches);

            $imageUrl = $imageMatches[1] ?? null;
            $caption = $descMatches[1] ?? null;

            if (!$imageUrl) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menemukan gambar otomatis. Silakan upload secara manual.']);
            }

            return response()->json([
                'success' => true,
                'image_url' => $imageUrl,
                'caption' => $caption
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menarik data: ' . $e->getMessage()]);
        }
    }
}
