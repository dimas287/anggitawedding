<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rundown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class RundownController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'time' => 'required',
            'activity' => 'required|string|max:200',
            'description' => 'nullable|string',
            'pic' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'sort_order' => 'nullable|integer',
        ]);

        $maxOrder = $booking->rundowns()->max('sort_order') ?? 0;
        Rundown::create([
            'booking_id' => $booking->id,
            'time' => $request->time,
            'activity' => $request->activity,
            'description' => $request->description,
            'pic' => $request->pic,
            'location' => $request->location,
            'duration_minutes' => $request->duration_minutes,
            'sort_order' => $request->sort_order ?? ($maxOrder + 1),
        ]);

        return back()->with('success', 'Rundown berhasil ditambahkan.');
    }

    public function update(Request $request, Rundown $rundown)
    {
        $request->validate([
            'time' => 'required',
            'activity' => 'required|string|max:200',
        ]);
        $rundown->update($request->only(['time', 'activity', 'description', 'pic', 'location', 'duration_minutes', 'sort_order', 'is_done', 'notes']));
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Rundown berhasil diperbarui.');
    }

    public function destroy(Rundown $rundown)
    {
        $rundown->delete();
        return back()->with('success', 'Item rundown dihapus.');
    }

    public function reorder(Request $request, Booking $booking)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $index => $id) {
            Rundown::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function exportPdf(Booking $booking)
    {
        $booking->load('rundowns', 'vendors', 'user', 'package');
        $rundowns = $booking->rundowns()->orderBy('sort_order')->orderBy('time')->get();
        $pdf = Pdf::loadView('pdf.rundown', compact('booking', 'rundowns'))->setPaper('a4');
        return $pdf->download('rundown-' . $booking->booking_code . '.pdf');
    }
}
