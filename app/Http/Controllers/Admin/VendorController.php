<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Booking;
use App\Models\BookingVendor;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();
        if ($request->category) $query->where('category', $request->category);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }
        $vendors = $query->orderBy('name')->paginate(15);
        $categories = Vendor::distinct()->pluck('category');
        return view('admin.vendors.index', compact('vendors', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'base_price' => 'nullable|numeric|min:0',
        ]);
        $data = $request->only(['name', 'category', 'phone', 'email', 'base_price']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('vendors', 'public');
        }
        Vendor::create($data);
        return back()->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
        ]);
        $data = $request->only(['name', 'category', 'phone', 'email', 'base_price', 'is_active']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('vendors', 'public');
        }
        $vendor->update($data);
        return back()->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->update(['is_active' => false]);
        return back()->with('success', 'Vendor dinonaktifkan.');
    }

    public function assignToBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'category' => 'required|string|max:50',
            'vendor_name' => 'required|string|max:100',
            'contact' => 'nullable|string|max:50',
            'cost' => 'required|numeric|min:0',
            'vendor_id' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
            'proof_attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $proofPath = $request->hasFile('proof_attachment')
            ? $request->file('proof_attachment')->store('vendor-payments', 'local')
            : null;

        $bookingVendor = BookingVendor::create([
            'booking_id' => $booking->id,
            'vendor_id' => $request->vendor_id,
            'category' => $request->category,
            'vendor_name' => $request->vendor_name,
            'contact' => $request->contact,
            'cost' => $request->cost ?? 0,
            'notes' => $request->notes,
            'status' => 'assigned',
            'proof_attachment' => $proofPath,
        ]);

        $this->syncVendorExpense($bookingVendor, auth()->id());

        return back()->with('success', 'Vendor berhasil di-assign.');
    }

    public function updateBookingVendor(Request $request, BookingVendor $bookingVendor)
    {
        $request->validate([
            'category' => 'required|string|max:50',
            'vendor_name' => 'required|string|max:100',
            'contact' => 'nullable|string|max:50',
            'status' => 'required|in:assigned,confirmed,done,cancelled',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'proof_attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'admin_password' => 'required|string',
        ]);

        [$authorized, $result] = $this->validateAdminPassword($request);
        if (!$authorized) {
            return $result;
        }
        $admin = $result;

        DB::transaction(function () use ($request, $bookingVendor, $admin) {
            $data = $request->only(['category', 'vendor_name', 'contact', 'status', 'cost', 'notes']);

            if ($request->hasFile('proof_attachment')) {
                if ($bookingVendor->proof_attachment) {
                    Storage::disk('local')->delete($bookingVendor->proof_attachment);
                }
                $data['proof_attachment'] = $request->file('proof_attachment')->store('vendor-payments', 'local');
            }

            $bookingVendor->update($data);
            $this->syncVendorExpense($bookingVendor, $admin->id);
        });

        return $this->successResponse($request, 'Vendor berhasil diperbarui.');
    }

    public function removeFromBooking(Request $request, BookingVendor $bookingVendor)
    {
        $request->validate(['admin_password' => 'required|string']);

        [$authorized, $result] = $this->validateAdminPassword($request);
        if (!$authorized) {
            return $result;
        }

        DB::transaction(function () use ($bookingVendor) {
            $this->deleteVendorExpense($bookingVendor);
            if ($bookingVendor->proof_attachment) {
                Storage::disk('local')->delete($bookingVendor->proof_attachment);
            }
            $bookingVendor->delete();
        });

        return $this->successResponse($request, 'Vendor dihapus dari booking.');
    }

    private function syncVendorExpense(BookingVendor $bookingVendor, ?int $adminId = null): void
    {
        $reference = $this->vendorReference($bookingVendor);
        $query = FinancialTransaction::where('reference', $reference);

        if ($bookingVendor->cost <= 0) {
            $query->delete();
            return;
        }

        $data = [
            'booking_id' => $bookingVendor->booking_id,
            'created_by' => $adminId ?? auth()->id(),
            'type' => 'expense',
            'category' => 'vendor_payment',
            'description' => 'Pembayaran vendor ' . $bookingVendor->vendor_name,
            'amount' => $bookingVendor->cost,
            'transaction_date' => now()->toDateString(),
            'reference' => $reference,
            'attachment' => $bookingVendor->proof_attachment,
            'notes' => $bookingVendor->notes,
        ];

        if ($existing = $query->first()) {
            $existing->update($data);
        } else {
            FinancialTransaction::create($data);
        }
    }

    private function deleteVendorExpense(BookingVendor $bookingVendor): void
    {
        FinancialTransaction::where('reference', $this->vendorReference($bookingVendor))->delete();
    }

    private function vendorReference(BookingVendor $bookingVendor): string
    {
        return 'booking-vendor-' . $bookingVendor->id;
    }

    private function validateAdminPassword(Request $request): array
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            return [false, $this->adminErrorResponse($request, 'Tidak memiliki akses admin.', 403)];
        }

        $throttleKey = sprintf('admin-password:%s:%s', $user->id, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.';
            return [false, $this->adminErrorResponse($request, $message, 429)];
        }

        if (!Hash::check($request->admin_password, $user->password)) {
            RateLimiter::hit($throttleKey, 300);
            return [false, $this->adminErrorResponse($request, 'Password admin salah.', 403)];
        }

        RateLimiter::clear($throttleKey);

        return [true, $user];
    }

    private function adminErrorResponse(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }

    private function successResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }
}
