<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioImageController extends Controller
{
    public function index()
    {
        $images = PortfolioImage::with('mediaItems')->orderBy('sort_order')->latest('id')->get();
        $nextSortOrder = (PortfolioImage::max('sort_order') ?? 0) + 1;

        return view('admin.portfolio.index', compact('images', 'nextSortOrder'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:5120',
            'title' => 'nullable|string|max:150',
            'caption' => 'nullable|string|max:255',
            'aspect' => 'nullable|in:square,portrait,landscape,wide,tall',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('portfolio', 'public');

        PortfolioImage::create([
            'image_path' => $path,
            'title' => $data['title'] ?? null,
            'caption' => $data['caption'] ?? null,
            'aspect' => $data['aspect'] ?? 'square',
            'sort_order' => $data['sort_order'] ?? ((PortfolioImage::max('sort_order') ?? 0) + 1),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Gambar portofolio berhasil ditambahkan.');
    }

    public function update(Request $request, PortfolioImage $portfolioImage)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:150',
            'caption' => 'nullable|string|max:255',
            'aspect' => 'nullable|in:square,portrait,landscape,wide,tall',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($portfolioImage->image_path) {
                Storage::disk('public')->delete($portfolioImage->image_path);
            }
            $data['image_path'] = $request->file('image')->store('portfolio', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $portfolioImage->update($data);

        return back()->with('success', 'Gambar portofolio berhasil diperbarui.');
    }

    public function destroy(PortfolioImage $portfolioImage)
    {
        if ($portfolioImage->image_path) {
            Storage::disk('public')->delete($portfolioImage->image_path);
        }
        $portfolioImage->delete();

        return back()->with('success', 'Gambar portofolio berhasil dihapus.');
    }
}
