<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,ppt,pptx,zip|max:10240',
            'category' => 'required|in:contract,invoice,photo,rundown,other',
        ]);

        $path = $request->file('file')->store('documents/' . $booking->id, 'local');
        Document::create([
            'booking_id' => $booking->id,
            'uploaded_by' => Auth::id(),
            'name' => $request->name,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientMimeType(),
            'file_size' => $request->file('file')->getSize(),
            'category' => $request->category,
            'is_visible_to_client' => true,
        ]);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function download(Document $document)
    {
        $booking = $document->booking;
        if (Auth::user()->isAdmin() === false && $booking->user_id != Auth::id()) {
            abort(403);
        }
        if (!$document->is_visible_to_client && !Auth::user()->isAdmin()) abort(403);
        return Storage::disk('local')->download($document->file_path, $document->name);
    }

    public function destroy(Document $document)
    {
        $booking = $document->booking;
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->isAdmin()) abort(403);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
