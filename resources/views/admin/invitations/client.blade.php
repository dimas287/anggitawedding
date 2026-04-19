@extends('layouts.admin')
@section('title', 'Kelola Undangan Klien')
@section('page-title', 'Undangan: ' . $booking->groom_name . ' & ' . $booking->bride_name)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="font-semibold text-gray-800">{{ $booking->booking_code }}</p>
            <p class="text-sm text-gray-500">{{ $booking->groom_name }} & {{ $booking->bride_name }} • {{ $booking->event_date->isoFormat('D MMM Y') }}</p>
        </div>
        <div class="flex gap-2">
            @if($invitation?->is_published)
            <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank"
               class="text-sm font-medium px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100">
                <i class="fas fa-external-link-alt mr-1"></i> Buka
            </a>
            @endif
            @if($invitation)
            <form action="{{ route('admin.invitation.reset-link', $booking->id) }}" method="POST">
                @csrf
                <button class="text-sm font-medium px-4 py-2 bg-gray-50 text-gray-600 rounded-xl hover:bg-gray-100">
                    <i class="fas fa-sync mr-1"></i> Reset Link
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ $invitation ? 'Edit' : 'Buat' }} Undangan</h3>
        <form action="{{ route('admin.invitation.client.update', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            {{-- Template --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Template</label>
                <select id="templateSelect" name="template_id" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    @foreach($templates as $tpl)
                    <option value="{{ $tpl->id }}" {{ $invitation?->template_id == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Data Pengantin --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3"><i class="fas fa-heart mr-1 text-yellow-500"></i> Data Pengantin</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengantin Pria <span class="text-red-500">*</span></label><input type="text" name="groom_name" value="{{ $invitation?->groom_name ?? $booking->groom_name }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Panggilan Pria</label><input type="text" name="groom_short_name" value="{{ $invitation?->groom_short_name }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengantin Wanita <span class="text-red-500">*</span></label><input type="text" name="bride_name" value="{{ $invitation?->bride_name ?? $booking->bride_name }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Panggilan Wanita</label><input type="text" name="bride_short_name" value="{{ $invitation?->bride_short_name }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ayah Pengantin Pria</label><input type="text" name="groom_father" value="{{ $invitation?->groom_father }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ibu Pengantin Pria</label><input type="text" name="groom_mother" value="{{ $invitation?->groom_mother }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ayah Pengantin Wanita</label><input type="text" name="bride_father" value="{{ $invitation?->bride_father }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ibu Pengantin Wanita</label><input type="text" name="bride_mother" value="{{ $invitation?->bride_mother }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                </div>
            </div>

            {{-- Akad Nikah --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3"><i class="fas fa-mosque mr-1 text-yellow-500"></i> Akad Nikah</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal & Waktu</label><input type="datetime-local" name="akad_datetime" value="{{ $invitation?->akad_datetime?->format('Y-m-d\TH:i') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tempat</label><input type="text" name="akad_venue" value="{{ $invitation?->akad_venue }}" placeholder="Nama gedung/masjid" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                </div>
                <div class="mt-3"><label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Akad</label><textarea name="akad_address" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $invitation?->akad_address }}</textarea></div>
            </div>

            {{-- Resepsi --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3"><i class="fas fa-glass-cheers mr-1 text-yellow-500"></i> Resepsi</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal & Waktu</label><input type="datetime-local" name="reception_datetime" value="{{ $invitation?->reception_datetime?->format('Y-m-d\TH:i') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tempat</label><input type="text" name="reception_venue" value="{{ $invitation?->reception_venue ?? $booking->venue }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                </div>
                <div class="mt-3"><label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Resepsi</label><textarea name="reception_address" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $invitation?->reception_address ?? $booking->venue_address }}</textarea></div>
                <div class="mt-3"><label class="block text-sm font-medium text-gray-700 mb-1.5">Link Google Maps</label><input type="url" name="maps_link" value="{{ $invitation?->maps_link }}" placeholder="https://maps.google.com/..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
            </div>

            {{-- Teks & Konten --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3"><i class="fas fa-pen-fancy mr-1 text-yellow-500"></i> Teks & Konten</h4>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Kutipan Pembuka</label><textarea name="opening_quote" rows="3" placeholder="Ayat Al-Quran atau kata-kata cinta..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $invitation?->opening_quote }}</textarea></div>
                <div class="mt-3"><label class="block text-sm font-medium text-gray-700 mb-1.5">Kisah Cinta (Opsional)</label><textarea name="love_story" rows="4" placeholder="Ceritakan bagaimana mereka bertemu..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $invitation?->love_story }}</textarea></div>
                <div class="mt-3"><label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan Penutup</label><textarea name="closing_message" rows="3" placeholder="Terima kasih atas doa restu..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ $invitation?->closing_message }}</textarea></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Hashtag</label><input type="text" name="hashtag" value="{{ $invitation?->hashtag }}" placeholder="#NamaKalian2024" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Musik Latar (MP3)</label><input type="file" name="music_file" accept=".mp3,.ogg,.wav" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:bg-yellow-50 file:text-yellow-700"></div>
                </div>
            </div>

            {{-- Media --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3"><i class="fas fa-images mr-1 text-yellow-500"></i> Foto & Media</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Prewedding</label>
                        <input type="file" name="photo_prewedding" accept="image/*" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-yellow-50 file:text-yellow-700">
                        @if($invitation?->photo_prewedding)
                            <a href="{{ asset('storage/' . $invitation->photo_prewedding) }}" target="_blank" class="text-xs text-yellow-600 underline mt-1 inline-block">Lihat foto saat ini</a>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Galeri Foto (multiple)</label>
                        <input type="file" name="gallery_photos[]" accept="image/*" multiple class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-yellow-50 file:text-yellow-700">
                        @if($invitation?->gallery_photos && count($invitation->gallery_photos))
                            <p class="text-xs text-yellow-600 mt-1">{{ count($invitation->gallery_photos) }} foto galeri tersimpan</p>
                        @endif
                    </div>
                </div>

                {{-- Dynamic Media Slots --}}
                <div id="dynamicMediaContainer" class="mt-4 pt-3 border-t border-gray-100" style="display: none;">
                    <h5 class="font-semibold text-gray-700 mb-1 text-sm">Media Khusus Tema</h5>
                    <p class="text-xs text-gray-500 mb-3">Upload foto/video sesuai kebutuhan tema terpilih.</p>
                    <div id="mediaSlotsList" class="space-y-3"></div>
                </div>
            </div>

            {{-- Hadiah Digital --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-3"><i class="fas fa-gift mr-1 text-yellow-500"></i> Hadiah Digital (Opsional)</h4>
                <p class="text-xs text-gray-500 mb-3">Tambahkan rekening bank atau QRIS untuk menerima hadiah digital.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Bank</label>
                        <input type="text" name="bank_name" placeholder="BCA / Mandiri / BNI" value="{{ $invitation?->bank_accounts[0]['bank_name'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Nomor Rekening</label>
                        <input type="text" name="bank_account" placeholder="1234567890" value="{{ $invitation?->bank_accounts[0]['account_number'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Pemilik Rekening</label>
                        <input type="text" name="bank_owner" placeholder="A/N Pemilik Rekening" value="{{ $invitation?->bank_accounts[0]['account_name'] ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Upload QRIS (Opsional)</label>
                        <input type="file" name="qris_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:bg-yellow-50 file:text-yellow-700">
                        @if($invitation?->qris_image)
                            <div class="mt-2 text-xs text-yellow-600 flex gap-4 items-center">
                                <a href="{{ route('invitation.qris', $invitation->slug) }}" target="_blank" class="underline">Lihat QRIS Saat Ini</a>
                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" name="clear_qris" value="1" class="rounded border-gray-300 text-yellow-500">
                                    <span>Hapus QRIS</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t">
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ $invitation?->is_published ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-500">
                    Publikasikan Undangan
                </label>
                <button type="submit" class="gold-gradient text-white font-bold px-6 py-3 rounded-xl text-sm hover:shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i> Simpan Undangan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const templatesData = @json($templates->keyBy('id'));
const existingMedia = @json($invitation?->media_files ?? []);

function renderMediaSlots(templateId) {
    const container = document.getElementById('dynamicMediaContainer');
    const list = document.getElementById('mediaSlotsList');
    list.innerHTML = '';
    
    if (!templateId || !templatesData[templateId] || !templatesData[templateId].media_slots || templatesData[templateId].media_slots.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    const slots = templatesData[templateId].media_slots;
    container.style.display = 'block';
    
    slots.forEach(slot => {
        const accept = slot.type === 'video' ? 'video/*' : 'image/*';
        const isMultiple = slot.max > 1;
        const inputName = isMultiple ? `media_slots[${slot.key}][]` : `media_slots[${slot.key}]`;
        const multipleAttr = isMultiple ? 'multiple' : '';
        
        let existingHtml = '';
        if (existingMedia[slot.key]) {
            let files = Array.isArray(existingMedia[slot.key]) ? existingMedia[slot.key] : [existingMedia[slot.key]];
            if (files.length > 0) {
                existingHtml = `<div class="mt-2 text-xs text-yellow-600 flex gap-2 flex-wrap">
                    ${files.map((file, i) => `<a href="/storage/${file}" target="_blank" class="underline">Lihat ${slot.type} ${i+1}</a>`).join('')}
                </div>`;
            }
        }

        const helpText = isMultiple ? `<p class="text-[11px] text-gray-400 mt-1 mb-2">Pilih hingga ${slot.max} ${slot.type}. Pilihan baru akan menimpa file lama.</p>` : '';

        list.innerHTML += `
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <label class="block text-sm font-medium text-gray-700 mb-1">${slot.label}</label>
                ${helpText}
                <input type="file" name="${inputName}" accept="${accept}" ${multipleAttr} class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:bg-yellow-50 file:text-yellow-700 focus:outline-none">
                ${existingHtml}
            </div>
        `;
    });
}

const templateSelect = document.getElementById('templateSelect');
if (templateSelect) {
    templateSelect.addEventListener('change', function() {
        renderMediaSlots(this.value);
    });
    // Initial render
    renderMediaSlots(templateSelect.value);
}
</script>
@endpush
