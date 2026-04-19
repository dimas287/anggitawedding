@extends('layouts.admin')
@section('title', 'Detail Konsultasi')
@section('page-title', 'Detail Konsultasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-gray-800 text-lg">{{ $consultation->consultation_code }}</h2>
                <p class="text-gray-500 text-sm">Diajukan {{ $consultation->created_at->diffForHumans() }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-full text-sm font-semibold
                {{ ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','done'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600','converted'=>'bg-purple-100 text-purple-700'][$consultation->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $consultation->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-5">
            @foreach(['Nama'=>$consultation->name,'Email'=>$consultation->email,'No. HP'=>$consultation->phone,'Tanggal Konsultasi'=>$consultation->preferred_date->isoFormat('dddd, D MMMM Y'),'Jam'=>$consultation->preferred_time.' WIB','Tipe'=>ucfirst($consultation->consultation_type),'Rencana Hari-H'=>$consultation->event_date?$consultation->event_date->isoFormat('D MMMM Y'):'-'] as $label => $val)
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-500 mb-0.5">{{ $label }}</p>
                <p class="font-medium text-gray-800">{{ $val }}</p>
            </div>
            @endforeach
        </div>

        @if($consultation->message)
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-gray-700 mb-5">
            <p class="text-xs font-semibold text-blue-600 mb-1">Pesan dari Klien:</p>
            {{ $consultation->message }}
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2 pb-5 border-b mb-5">
            @foreach(['confirmed'=>'Konfirmasi','done'=>'Tandai Selesai','cancelled'=>'Batalkan'] as $status => $label)
            @if($consultation->status !== $status)
            <form action="{{ route('admin.consultations.status', $consultation->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengubah status ke {{ strtolower($label) }}?');">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $status }}">
                <button class="text-xs font-semibold px-4 py-2 rounded-xl border transition-colors
                    {{ $status==='confirmed'? 'border-green-400 text-green-700 hover:bg-green-50' : ($status==='done'? 'border-blue-400 text-blue-700 hover:bg-blue-50' : 'border-red-400 text-red-600 hover:bg-red-50') }}">
                    {{ $label }}
                </button>
            </form>
            @endif
            @endforeach
            <form action="{{ route('admin.consultations.reminder', $consultation->id) }}" method="POST">
                @csrf
                <button class="text-xs font-semibold px-4 py-2 rounded-xl border border-yellow-400 text-yellow-700 hover:bg-yellow-50 transition-colors">
                    <i class="fas fa-bell mr-1"></i> Kirim Reminder
                </button>
            </form>
        </div>

        @if(!in_array($consultation->status, ['cancelled','done']))
        <div class="bg-white border border-dashed border-gray-200 rounded-xl p-5 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Penjadwalan Ulang</p>
                    <p class="text-xs text-gray-500">Pastikan klien sudah menyetujui jadwal baru via WhatsApp sebelum menyimpan.</p>
                </div>
            </div>
            <form action="{{ route('admin.consultations.reschedule', $consultation->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Baru</label>
                    <input type="date" name="preferred_date" value="{{ old('preferred_date', optional($consultation->preferred_date)->format('Y-m-d')) }}" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Baru</label>
                    <input type="time" name="preferred_time" value="{{ old('preferred_time', $consultation->preferred_time) }}" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Konsultasi</label>
                    <select name="consultation_type" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" required>
                        <option value="online" {{ old('consultation_type', $consultation->consultation_type) === 'online' ? 'selected' : '' }}>Online (Video Call)</option>
                        <option value="offline" {{ old('consultation_type', $consultation->consultation_type) === 'offline' ? 'selected' : '' }}>Offline (Kantor)</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex flex-col gap-2">
                    <button type="submit" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm">Simpan Jadwal Baru & Kirim Email</button>
                    <p class="text-[11px] text-gray-500">Sistem otomatis mengirim email konfirmasi baru dan menjadwalkan reminder H-3 jam.</p>
                </div>
            </form>
        </div>
        @endif

        {{-- Meeting Notes --}}
        <h3 class="font-semibold text-gray-800 mb-3">Catatan Meeting</h3>
        <form action="{{ route('admin.consultations.notes', $consultation->id) }}" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Meeting</label>
                <textarea name="meeting_notes" rows="4" placeholder="Tuliskan hasil diskusi konsultasi..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $consultation->meeting_notes }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Follow-up / Tindak Lanjut</label>
                <textarea name="followup_notes" rows="3" placeholder="Rencana follow-up atau tindak lanjut..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $consultation->followup_notes }}</textarea>
            </div>
            <button type="submit" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm">
                <i class="fas fa-save mr-2"></i> Simpan Catatan
            </button>
        </form>
    </div>

    @if($consultation->status === 'done' && !$consultation->booking_id)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Konversi ke Booking Paket</h3>
        <form action="{{ route('admin.consultations.convert', $consultation->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @csrf
            <select name="package_id" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Pilih Paket...</option>
                @foreach(\App\Models\Package::where('is_active',true)->get() as $pkg)
                <option value="{{ $pkg->id }}">{{ $pkg->name }} – {{ $pkg->formatted_price }}</option>
                @endforeach
            </select>
            <input type="date" name="event_date" required min="{{ date('Y-m-d') }}" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <input type="text" name="groom_name" placeholder="Nama Pengantin Pria" value="{{ $consultation->name }}" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <input type="text" name="bride_name" placeholder="Nama Pengantin Wanita" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <input type="text" name="venue" placeholder="Venue" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 sm:col-span-2">
            <button type="submit" class="sm:col-span-2 gold-gradient text-white font-bold py-3 rounded-xl text-sm hover:shadow-lg transition-all">
                <i class="fas fa-arrow-right mr-2"></i> Buat Booking dari Konsultasi
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
