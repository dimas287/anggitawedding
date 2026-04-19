<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingExtraCharge;
use App\Models\BookingFitting;
use App\Models\BookingWardrobeItem;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Package;
use App\Models\FinancialTransaction;
use App\Models\User;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingInvoiceMail;
use App\Mail\PaymentSuccessMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('user', 'package')
            ->weddingPackages()
            ->latest();

        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhere('groom_name', 'like', '%' . $request->search . '%')
                  ->orWhere('bride_name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->date_from) $query->whereDate('event_date', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('event_date', '<=', $request->date_to);

        $bookings = $query->paginate(15);
        $packages = Package::where('is_active', true)->orderBy('name')->get();
        $clients = User::where('role', 'client')->orderBy('name')->get(['id', 'name', 'email', 'phone']);

        return view('admin.bookings.index', compact('bookings', 'packages', 'clients'));
    }

    public function store(Request $request)
    {
        $mode = $request->input('client_mode', 'existing');

        $rules = [
            'client_mode' => ['required', Rule::in(['existing', 'new'])],
            'package_id' => 'required|exists:packages,id',
            'groom_name' => 'required|string|max:120',
            'groom_short_name' => 'required|string|max:60',
            'bride_name' => 'required|string|max:120',
            'bride_short_name' => 'required|string|max:60',
            'event_date' => 'required|date|after:today',
            'venue' => 'required|string|max:255',
            'venue_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $emailRules = ['required', 'email', 'max:150'];
        $phoneRules = ['required', 'string', 'max:50'];

        if ($mode === 'existing') {
            $rules['user_id'] = ['required', 'exists:users,id'];
        } else {
            $rules['client_name'] = ['required', 'string', 'max:120'];
            $emailRules[] = Rule::unique('users', 'email');
            $phoneRules[] = Rule::unique('users', 'phone');
        }

        $rules['email'] = $emailRules;
        $rules['phone'] = $phoneRules;

        $data = $request->validate($rules);

        $package = Package::findOrFail($data['package_id']);

        if ($mode === 'existing') {
            $user = User::findOrFail($data['user_id']);
            $temporaryPassword = null;
        } else {
            [$user, $temporaryPassword] = $this->createClientFromAdmin($data);
        }

        $existingBooking = Booking::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->whereDate('event_date', $data['event_date'])
            ->where('payment_status', 'unpaid')
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if ($existingBooking) {
            return redirect()->route('admin.bookings.show', $existingBooking->id)
                ->with('info', 'Booking serupa sudah ada dan masih menunggu pembayaran.');
        }

        $booking = DB::transaction(function () use ($data, $package, $user) {
            $booking = new Booking([
                'booking_code' => Booking::generateCode(),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'groom_name' => $data['groom_name'],
                'groom_short_name' => $data['groom_short_name'],
                'bride_name' => $data['bride_name'],
                'bride_short_name' => $data['bride_short_name'],
                'event_date' => $data['event_date'],
                'venue' => $data['venue'],
                'venue_address' => $data['venue_address'] ?? null,
                'phone' => $data['phone'],
                'email' => $data['email'],
                'notes' => $data['notes'] ?? null,
                'package_price' => $package->price,
                'dp_amount' => $package->price * 0.30,
            ]);
            $booking->total_paid = 0;
            $booking->status = 'pending';
            $booking->payment_status = 'unpaid';
            $booking->save();
            return $booking;
        });

        try {
            Mail::to($booking->email)->send(new BookingConfirmationMail($booking));
        } catch (\Exception $e) {
            // ignore send failure
        }

        $message = 'Booking manual berhasil dibuat dan email konfirmasi dikirim.';
        if (!empty($temporaryPassword)) {
            $message .= ' Akun klien baru dibuat. Password sementara: ' . $temporaryPassword;
        }

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', $message);
    }

    protected function createClientFromAdmin(array $data): array
    {
        $temporaryPassword = Str::random(12);

        $user = User::create([
            'name' => $data['client_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => 'client',
            'is_active' => true,
            'password' => $temporaryPassword,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        return [$user, $temporaryPassword];
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'user', 'package', 'payments', 'extraCharges', 'invitation', 'consultations',
            'vendors', 'rundowns', 'documents', 'financialTransactions', 'review', 'wardrobeItems',
            'fittings' => function ($query) {
                $query->latest('scheduled_at')->with('creator');
            },
        ]);
        $packages = Package::where('is_active', true)->orderBy('name')->get();
        return view('admin.bookings.show', compact('booking', 'packages'));
    }

    public function addFitting(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:160',
            'focus' => 'nullable|string|max:120',
            'notes' => 'nullable|string',
        ]);

        $booking->fittings()->create([
            'scheduled_at' => $data['scheduled_at'],
            'location' => $data['location'] ?? null,
            'focus' => $data['focus'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Jadwal fitting berhasil ditambahkan.');
    }

    public function deleteFitting(Booking $booking, BookingFitting $fitting)
    {
        abort_if($fitting->booking_id !== $booking->id, 404);
        $fitting->delete();

        return back()->with('success', 'Jadwal fitting dihapus.');
    }

    public function addWardrobeItem(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'item_name' => 'required|string|max:160',
            'wearer' => 'nullable|string|max:120',
            'category' => 'nullable|string|max:120',
            'size' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:60',
            'accessories' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $booking->wardrobeItems()->create(array_merge($data, [
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Data wardrobe berhasil disimpan.');
    }

    public function deleteWardrobeItem(Booking $booking, BookingWardrobeItem $item)
    {
        abort_if($item->booking_id !== $booking->id, 404);
        $item->delete();

        return back()->with('success', 'Data wardrobe dihapus.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required|in:pending,dp_paid,in_progress,completed,cancelled']);
        $booking->status = $request->status;
        
        if ($request->status === 'completed') {
            $booking->is_locked = true;
            $booking->locked_at = now();
        }
        $booking->save();

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function addOfflinePayment(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'type' => 'required|in:dp,installment,full,offline',
            'notes' => 'nullable|string',
            'proof_attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $proofPath = $request->hasFile('proof_attachment')
            ? $request->file('proof_attachment')->store('payment-proofs', 'local')
            : null;

        $payment = Payment::create([
            'payment_code' => Payment::generateCode(),
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'method' => 'cash',
            'status' => 'success',
            'notes' => $request->notes,
            'proof_attachment' => $proofPath,
            'paid_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        $newTotal = $booking->total_paid + $request->amount;
        $isFullyPaid = $newTotal >= $booking->package_price;

        $booking->total_paid = $newTotal;
        $booking->payment_status = $isFullyPaid ? 'paid_full' : ($booking->status === 'pending' ? 'dp_paid' : 'partially_paid');
        $booking->status = $booking->status === 'pending' ? 'dp_paid' : $booking->status;
        $booking->save();

        FinancialTransaction::create([
            'booking_id' => $booking->id,
            'created_by' => auth()->id(),
            'type' => 'income',
            'category' => 'offline_payment',
            'description' => 'Pembayaran offline - ' . $booking->booking_code,
            'amount' => $request->amount,
            'transaction_date' => now()->toDateString(),
            'reference' => $payment->payment_code,
            'attachment' => $proofPath,
        ]);

        try {
            Mail::to($booking->user->email)->send(new PaymentSuccessMail($booking, $payment));
        } catch (\Exception $e) {}

        return back()->with('success', 'Pembayaran offline berhasil dicatat.');
    }

    public function addExtraCharge(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'amount' => 'required|numeric|min:1000',
            'charge_type' => 'required|in:addition,discount',
            'notes' => 'nullable|string',
        ]);

        $amount = (float) $data['amount'];
        if ($data['charge_type'] === 'discount') {
            $amount *= -1;
        }

        $booking->extraCharges()->create([
            'title' => $data['title'],
            'amount' => $amount,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Biaya tambahan berhasil ditambahkan.');
    }

    public function updateExtraCharge(Request $request, Booking $booking, BookingExtraCharge $charge)
    {
        abort_if($charge->booking_id !== $booking->id, 404);

        $data = $request->validate([
            'title' => 'required|string|max:160',
            'amount' => 'required|numeric|min:1000',
            'charge_type' => 'required|in:addition,discount',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,billed,paid,waived',
        ]);

        $amount = (float) $data['amount'];
        if ($data['charge_type'] === 'discount') {
            $amount *= -1;
        }

        $charge->update([
            'title' => $data['title'],
            'amount' => $amount,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Biaya tambahan berhasil diperbarui.');
    }

    public function deleteExtraCharge(Booking $booking, BookingExtraCharge $charge)
    {
        abort_if($charge->booking_id !== $booking->id, 404);

        $charge->delete();

        return back()->with('success', 'Biaya tambahan berhasil dihapus.');
    }

    public function sendInvoiceEmail(Booking $booking)
    {
        $booking->load('user', 'package', 'payments', 'extraCharges');
        $extraTotal = $booking->active_extra_charges_total;

        try {
            $pdfContent = Pdf::loadView('pdf.invoice', [
                'booking' => $booking,
                'extraTotal' => $extraTotal,
                'grandTotal' => $booking->package_price + $extraTotal,
            ])->output();
            $recipient = $booking->email ?: optional($booking->user)->email;
            if (!$recipient) {
                return back()->with('error', 'Email klien tidak tersedia.');
            }
            Mail::to($recipient)->send(new BookingInvoiceMail($booking, $pdfContent));
            return back()->with('success', 'Invoice berhasil dikirim ke email klien.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim invoice: ' . $e->getMessage());
        }
    }

    public function convertConsultation(Request $request, Consultation $consultation)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date|after:today',
            'venue' => 'required|string',
            'groom_name' => 'required|string|max:120',
            'groom_short_name' => 'required|string|max:60',
            'bride_name' => 'required|string|max:120',
            'bride_short_name' => 'required|string|max:60',
        ]);

        $package = Package::findOrFail($request->package_id);

        $booking = Booking::create([
            'booking_code' => Booking::generateCode(),
            'user_id' => $consultation->user_id,
            'package_id' => $package->id,
            'groom_name' => $request->groom_name ?? $consultation->name,
            'groom_short_name' => $request->groom_short_name ?? $request->groom_name ?? $consultation->name,
            'bride_name' => $request->bride_name ?? '',
            'bride_short_name' => $request->bride_short_name ?? ($request->bride_name ?? ''),
            'event_date' => $request->event_date,
            'venue' => $request->venue,
            'phone' => $consultation->phone,
            'email' => $consultation->email,
            'notes' => $consultation->message,
            'package_price' => $package->price,
            'dp_amount' => $package->price * 0.30,
            'total_paid' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $consultation->update(['status' => 'converted', 'booking_id' => $booking->id]);

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Konsultasi berhasil dikonversi ke booking paket.');
    }

    public function updateAdminNotes(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
            'client_notes' => 'nullable|string|max:2000',
        ]);

        $booking->admin_notes = $request->admin_notes;
        $booking->client_notes = $request->client_notes;
        $booking->save();

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function updateInfo(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'groom_name' => 'required|string|max:120',
            'groom_short_name' => 'nullable|string|max:60',
            'bride_name' => 'required|string|max:120',
            'bride_short_name' => 'nullable|string|max:60',
            'event_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'notes' => 'nullable|string',
        ]);

        $booking->update($data);

        return back()->with('success', 'Data booking berhasil diperbarui.');
    }

    public function destroy(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'admin_password' => ['required', 'string'],
        ]);

        if ($booking->status !== 'cancelled') {
            return back()->with('error', 'Booking hanya dapat dihapus apabila status sudah dibatalkan.');
        }

        if (!Hash::check($validated['admin_password'], $request->user()->password)) {
            return back()->with('error', 'Password admin tidak sesuai.');
        }

        try {
            DB::transaction(function () use ($booking) {
                $booking->payments()->delete();
                $booking->extraCharges()->delete();
                $booking->vendors()->delete();
                $booking->rundowns()->delete();
                $booking->fittings()->delete();
                $booking->wardrobeItems()->delete();
                $booking->documents()->delete();
                $booking->financialTransactions()->delete();
                $booking->chats()->delete();
                $booking->consultations()->update(['booking_id' => null]);

                if ($booking->invitation) {
                    $booking->invitation()->delete();
                }

                if ($booking->review) {
                    $booking->review()->delete();
                }

                $booking->delete();
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Booking gagal dihapus. Silakan coba lagi.');
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil dihapus.');
    }
}
