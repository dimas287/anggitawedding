@extends('layouts.app')
@section('title', 'Edit Undangan')
@section('page-title', 'Edit Undangan Digital')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('user.invitation.index', $booking->id) }}" class="text-yellow-600 hover:underline text-sm flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Undangan
        </a>
    </div>

    {{-- Template Selector --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="font-semibold text-gray-800">Pilih Template</h3>
                @if($invitation?->is_published)
                    <p class="text-xs text-red-500 font-medium mt-1 mb-1"><i class="fas fa-lock mr-1"></i> Undangan sedang dipublikasikan. Untuk mengubah tema, silakan Sembunyikan (Unpublish) dikanulas halaman dashboard terlebih dahulu.</p>
                @endif
                <p class="text-xs text-gray-500">{{ $includesDigitalInvitation ? 'Langsung pilih desain favoritmu.' : 'Harga mengikuti kartu template di bawah.' }}</p>
            </div>
            @unless($includesDigitalInvitation)
            <div class="text-right">
                <p class="text-[11px] text-gray-400 leading-tight">Belum termasuk paket</p>
                <a href="{{ route('user.chat.index', $booking->id) }}" class="text-xs text-yellow-600 hover:underline">Hubungi admin</a>
            </div>
            @endunless
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($templates as $tpl)
            <label class="cursor-pointer {{ $invitation?->is_published ? 'opacity-60 grayscale' : '' }}">
                <input type="radio" name="template_radio" value="{{ $tpl->id }}"
                       {{ $invitation?->template_id == $tpl->id ? 'checked' : '' }}
                       {{ $invitation?->is_published ? 'disabled' : '' }}
                       class="sr-only peer">
                <div class="rounded-xl border-2 p-3 text-center transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-50 hover:border-yellow-300"
                     style="border-color: {{ $invitation?->template_id == $tpl->id ? '#D4AF37' : '#E5E7EB' }}">
                    <div class="h-12 rounded-lg mb-2 flex items-center justify-center" style="background: {{ $tpl->primary_color }}">
                        <span class="text-white text-xs font-semibold">{{ $tpl->name }}</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ ucfirst($tpl->theme) }}</p>
                    @if($includesDigitalInvitation)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-semibold mt-1">
                            <i class="fas fa-check-circle"></i> Termasuk Paket
                        </span>
                    @else
                        <div class="mt-2 space-y-0.5">
                            @if($tpl->has_active_promo)
                                <p class="text-[11px] text-gray-400 line-through">{{ $tpl->formatted_price }}</p>
                                <p class="text-sm font-semibold text-yellow-600">{{ $tpl->formatted_effective_price }}</p>
                                @if($tpl->promo_label)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-pink-50 text-pink-600 text-[10px] font-semibold">{{ $tpl->promo_label }}</span>
                                @endif
                            @else
                                <p class="text-sm font-semibold text-gray-800">{{ $tpl->formatted_price }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <form action="{{ route('user.invitation.update', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        {{-- Hidden template id --}}
        <input type="hidden" name="template_id" id="templateIdField" value="{{ $invitation?->template_id }}">

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Data Pengantin</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengantin Pria <span class="text-red-500">*</span></label>
                    <input type="text" name="groom_name" value="{{ old('groom_name', $invitation?->groom_name ?? $booking->groom_name) }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengantin Wanita <span class="text-red-500">*</span></label>
                    <input type="text" name="bride_name" value="{{ old('bride_name', $invitation?->bride_name ?? $booking->bride_name) }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ayah Pengantin Pria</label><input type="text" name="groom_father" value="{{ old('groom_father', $invitation?->groom_father) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ibu Pengantin Pria</label><input type="text" name="groom_mother" value="{{ old('groom_mother', $invitation?->groom_mother) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ayah Pengantin Wanita</label><input type="text" name="bride_father" value="{{ old('bride_father', $invitation?->bride_father) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ibu Pengantin Wanita</label><input type="text" name="bride_mother" value="{{ old('bride_mother', $invitation?->bride_mother) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Akad Nikah</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal & Waktu</label><input type="datetime-local" name="akad_datetime" value="{{ old('akad_datetime', $invitation?->akad_datetime?->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tempat</label><input type="text" name="akad_venue" value="{{ old('akad_venue', $invitation?->akad_venue) }}" placeholder="Nama gedung/masjid" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label><textarea name="akad_address" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ old('akad_address', $invitation?->akad_address) }}</textarea></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Resepsi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal & Waktu</label><input type="datetime-local" name="reception_datetime" value="{{ old('reception_datetime', $invitation?->reception_datetime?->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tempat</label><input type="text" name="reception_venue" value="{{ old('reception_venue', $invitation?->reception_venue ?? $booking->venue) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label><textarea name="reception_address" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ old('reception_address', $invitation?->reception_address ?? $booking->venue_address) }}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Link Google Maps</label><input type="url" name="maps_link" value="{{ old('maps_link', $invitation?->maps_link) }}" placeholder="https://maps.google.com/..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-3">Teks & Konten</h3>
            <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Kutipan Pembuka</label><textarea name="opening_quote" rows="3" placeholder="Ayat Al-Quran atau kata-kata cinta..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ old('opening_quote', $invitation?->opening_quote) }}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Kisah Cinta (Opsional)</label><textarea name="love_story" rows="4" placeholder="Ceritakan bagaimana kalian bertemu..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ old('love_story', $invitation?->love_story) }}</textarea></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Hashtag</label><input type="text" name="hashtag" value="{{ old('hashtag', $invitation?->hashtag) }}" placeholder="#NamaKalian2024" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Musik Latar (MP3)</label><input type="file" name="music_file" accept=".mp3,.ogg,.wav" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:bg-yellow-50 file:text-yellow-700"></div>
            </div>

            <!-- Digital Gift Section -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <h4 class="font-semibold text-gray-800 mb-1 text-sm">Hadiah Digital (Opsional)</h4>
                <p class="text-xs text-gray-500 mb-4">Tambahkan rekening bank atau QRIS untuk menerima hadiah digital.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Bank</label>
                        <input type="text" name="bank_name" placeholder="BCA / Mandiri / BNI" value="{{ old('bank_name', $invitation?->bank_accounts[0]['bank_name'] ?? '') }}" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Nomor Rekening</label>
                        <input type="text" name="bank_account" placeholder="1234567890" value="{{ old('bank_account', $invitation?->bank_accounts[0]['account_number'] ?? '') }}" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Pemilik Rekening</label>
                        <input type="text" name="bank_owner" placeholder="A/N Pemilik Rekening" value="{{ old('bank_owner', $invitation?->bank_accounts[0]['account_name'] ?? '') }}" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
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

            <div id="dynamicMediaContainer" class="mt-6 pt-4 border-t border-gray-100" style="display: none;">
                <h4 class="font-semibold text-gray-800 mb-1 text-sm">Media Tema</h4>
                <p class="text-xs text-gray-500 mb-4">Upload foto/video sesuai kebutuhan tema ini.</p>
                <div id="mediaSlotsList" class="space-y-4"></div>
            </div>
        </div>

        <button type="submit" class="w-full gold-gradient text-white font-bold py-4 rounded-xl text-sm hover:shadow-lg transition-all">
            <i class="fas fa-save mr-2"></i> Simpan Undangan
        </button>
    </form>
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

document.querySelectorAll('input[name="template_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('templateIdField').value = this.value;
        renderMediaSlots(this.value);
    });
});

// Initial render
window.addEventListener('DOMContentLoaded', () => {
    const selected = document.querySelector('input[name="template_radio"]:checked');
    if(selected) renderMediaSlots(selected.value);
});
</script>
@endpush
