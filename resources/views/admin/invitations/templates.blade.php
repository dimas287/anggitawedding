@extends('layouts.admin')
@section('title', 'Template Undangan')
@section('page-title', 'Template Undangan Digital')

@section('content')
<div class="space-y-5" x-data="{ addOpen: false, editData: null, slots: [] }" x-init="$watch('editData', val => slots = val?.media_slots || [])">
    <div class="flex justify-end">
        <button @click="addOpen = true" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm hover:shadow-md">
            <i class="fas fa-plus mr-2"></i> Tambah Template
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($templates as $tpl)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all">
            <div class="relative h-40 bg-gray-100">
                @if($tpl->thumbnail)
                    <img src="{{ asset('storage/' . $tpl->thumbnail) }}" alt="{{ $tpl->name }} thumbnail" class="w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-envelope-open-text text-3xl mb-1"></i>
                        <p class="text-xs">Belum ada thumbnail</p>
                    </div>
                @endif
                <div class="absolute top-3 left-3 inline-flex items-center gap-1 bg-black/60 text-white text-[11px] font-semibold px-3 py-1 rounded-full capitalize">
                    <span class="w-2 h-2 rounded-full" style="background: {{ $tpl->primary_color }}"></span>
                    {{ $tpl->theme }}
                </div>
                @if($tpl->preview_image || $tpl->demo_url)
                <a href="{{ $tpl->demo_url ?? asset('storage/' . $tpl->preview_image) }}" target="_blank"
                   class="absolute bottom-3 right-3 text-[11px] font-semibold px-3 py-1 rounded-full bg-white/90 text-gray-700 shadow">
                    <i class="fas fa-external-link-alt mr-1"></i>Preview
                </a>
                @endif
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="font-semibold text-gray-800">{{ $tpl->name }}</p>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tpl->is_premium ? 'bg-purple-50 text-purple-600' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ $tpl->is_premium ? 'Premium' : 'Standar' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $tpl->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $tpl->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2 text-xs mb-3">
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ ucfirst($tpl->theme) }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $tpl->font_family }}</span>
                    @if($tpl->has_active_promo)
                        <span class="px-2 py-0.5 rounded-full bg-pink-50 text-pink-600 font-semibold">Promo</span>
                    @endif
                </div>
                <div class="mb-3 text-sm">
                    @if($tpl->has_active_promo)
                        <div class="text-gray-400 line-through text-xs">{{ $tpl->formatted_price }}</div>
                        <div class="text-xl font-bold text-yellow-600">{{ $tpl->formatted_effective_price }}</div>
                        @if($tpl->promo_label)
                            <p class="text-xs text-pink-600 mt-1">{{ $tpl->promo_label }}</p>
                        @endif
                    @else
                        <div class="text-xl font-bold text-gray-800">{{ $tpl->formatted_price }}</div>
                    @endif
                </div>
                <div class="flex gap-2 items-center">
                    <div class="flex gap-1 items-center flex-1">
                        <span class="w-5 h-5 rounded-full border border-gray-200" style="background: {{ $tpl->primary_color }}" title="Primary"></span>
                        <span class="w-5 h-5 rounded-full border border-gray-200" style="background: {{ $tpl->secondary_color }}" title="Secondary"></span>
                        <span class="text-xs text-gray-400 ml-1">{{ $tpl->primary_color }}</span>
                    </div>
                    <button @click="editData = @js($tpl)" class="text-xs text-yellow-600 hover:underline"><i class="fas fa-edit"></i></button>
                    <form action="{{ route('admin.invitation.templates.destroy', $tpl->id) }}" method="POST" onsubmit="return confirm('Hapus template?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-400">Belum ada template</div>
        @endforelse
    </div>

    {{-- Add Modal --}}
    <div x-show="addOpen || editData" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div @click.outside="addOpen = false; editData = null" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 my-4 max-h-[90vh] overflow-y-auto">
            <h3 class="font-semibold text-gray-800 mb-4" x-text="editData ? 'Edit Template' : 'Tambah Template Baru'"></h3>
            <form
                x-ref="templateForm"
                x-effect="$refs.templateForm.action = editData ? '{{ url('admin/template-undangan') }}/' + editData.id : '{{ route('admin.invitation.templates.store') }}'"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-3"
            >
                @csrf
                <template x-if="editData">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <input type="text" name="name" :value="editData?.name" placeholder="Nama Template *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <select name="theme" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    @foreach(['classic','minimalist','floral','royal','garden','bohemian','modern'] as $theme)
                    <option value="{{ $theme }}" :selected="editData?.theme === '{{ $theme }}'">{{ ucfirst($theme) }}</option>
                    @endforeach
                </select>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Warna Utama</label>
                        <input type="color" name="primary_color" :value="editData?.primary_color ?? '#D4AF37'" class="w-full h-12 rounded-xl border border-gray-200 cursor-pointer p-1">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Warna Latar</label>
                        <input type="color" name="secondary_color" :value="editData?.secondary_color ?? '#FFFBF0'" class="w-full h-12 rounded-xl border border-gray-200 cursor-pointer p-1">
                    </div>
                </div>
                <select name="font_family" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    @foreach(['Playfair Display','Great Vibes','Dancing Script','Cormorant Garamond','Josefin Sans','Poppins'] as $f)
                    <option value="{{ $f }}" :selected="editData?.font_family === '{{ $f }}'">{{ $f }}</option>
                    @endforeach
                </select>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Slug Demo (opsional)</label>
                    <input type="text" name="demo_slug" :value="editData?.demo_slug" placeholder="contoh: demo-aurora-lux" class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                    <p class="text-[11px] text-gray-400 mt-1">Jika diisi, tombol Preview akan membuka undangan demo via <code>/undangan/slug</code>.</p>
                </div>
                <div class="border border-yellow-100 bg-yellow-50/60 rounded-2xl p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-yellow-700">Data Demo Undangan</p>
                        <span class="text-[11px] text-yellow-600">Konten ini tampil saat tombol Preview diklik.</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Pengantin Pria</label>
                            <input type="text" name="demo_groom_name" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.groom_name ?? '') : @js(old('demo_groom_name'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Nama Pengantin Wanita</label>
                            <input type="text" name="demo_bride_name" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.bride_name ?? '') : @js(old('demo_bride_name'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Orang Tua Pengantin Pria</label>
                            <input type="text" name="demo_groom_father" placeholder="Ayah" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.groom_father ?? '') : @js(old('demo_groom_father'))">
                            <input type="text" name="demo_groom_mother" placeholder="Ibu" class="mt-2 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.groom_mother ?? '') : @js(old('demo_groom_mother'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Orang Tua Pengantin Wanita</label>
                            <input type="text" name="demo_bride_father" placeholder="Ayah" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.bride_father ?? '') : @js(old('demo_bride_father'))">
                            <input type="text" name="demo_bride_mother" placeholder="Ibu" class="mt-2 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.bride_mother ?? '') : @js(old('demo_bride_mother'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Tanggal Akad</label>
                            <input type="date" name="demo_akad_datetime" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.akad_datetime ? editData.parsed_demo_content.akad_datetime.slice(0,10) : '') : @js(old('demo_akad_datetime'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Tanggal Resepsi</label>
                            <input type="date" name="demo_reception_datetime" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.reception_datetime ? editData.parsed_demo_content.reception_datetime.slice(0,10) : '') : @js(old('demo_reception_datetime'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Lokasi Akad</label>
                            <input type="text" name="demo_akad_venue" placeholder="Gedung / Tempat" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.akad_venue ?? '') : @js(old('demo_akad_venue'))">
                            <input type="text" name="demo_akad_address" placeholder="Alamat lengkap" class="mt-2 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.akad_address ?? '') : @js(old('demo_akad_address'))">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Lokasi Resepsi</label>
                            <input type="text" name="demo_reception_venue" placeholder="Gedung / Tempat" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.reception_venue ?? '') : @js(old('demo_reception_venue'))">
                            <input type="text" name="demo_reception_address" placeholder="Alamat lengkap" class="mt-2 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.reception_address ?? '') : @js(old('demo_reception_address'))">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Link Maps</label>
                        <input type="text" name="demo_maps_link" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.maps_link ?? '') : @js(old('demo_maps_link'))">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Kutipan Pembuka</label>
                            <textarea name="demo_opening_quote" rows="2" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" x-text="editData ? (editData.parsed_demo_content?.opening_quote ?? '') : @js(old('demo_opening_quote'))"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Pesan Penutup</label>
                            <textarea name="demo_closing_message" rows="2" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" x-text="editData ? (editData.parsed_demo_content?.closing_message ?? '') : @js(old('demo_closing_message'))"></textarea>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Cerita Cinta</label>
                        <textarea name="demo_love_story" rows="3" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" x-text="editData ? (editData.parsed_demo_content?.love_story ?? '') : @js(old('demo_love_story'))"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Hashtag</label>
                            <input type="text" name="demo_hashtag" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.hashtag ?? '') : @js(old('demo_hashtag'))">
                        </div>
                        <div class="flex items-center gap-2 mt-5">
                            <input type="hidden" name="demo_rsvp_enabled" value="0">
                            <input type="checkbox" name="demo_rsvp_enabled" value="1" class="rounded border-gray-300 text-yellow-500" :checked="editData ? !!editData.parsed_demo_content?.rsvp_enabled : @js(old('demo_rsvp_enabled', true))">
                            <span class="text-xs font-semibold text-gray-600">Aktifkan formulir RSVP di demo</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Musik Latar Demo</label>
                            <input type="file" name="demo_music_file" accept="audio/*" class="mt-1 w-full border border-dashed border-yellow-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                            <template x-if="editData?.parsed_demo_content?.music_file_url">
                                <div class="mt-1 flex items-center gap-3 text-xs text-yellow-700">
                                    <a :href="editData.parsed_demo_content.music_file_url" target="_blank" class="underline">Unduh musik demo</a>
                                    <label class="inline-flex items-center gap-1">
                                        <input type="checkbox" name="demo_clear_music" value="1" class="rounded border-gray-300 text-yellow-500">
                                        <span>Hapus musik</span>
                                    </label>
                                </div>
                            </template>
                        </div>

                        <!-- Dynamic Demo Media Slots -->
                        <div class="pt-2 border-t border-yellow-200/50">
                            <p class="text-sm font-semibold text-yellow-700 mb-2">Demo Media Dinamis</p>
                            <p class="text-[11px] text-yellow-600 mb-3">Upload file demo berdasarkan slot media yang telah diatur di bawah ini.</p>
                            
                            <template x-if="slots.length === 0">
                                <p class="text-xs text-yellow-600 italic">Belum ada slot media. Tambahkan di bagian Slot Media Dinamis terlebih dahulu.</p>
                            </template>
                            
                            <div class="space-y-4">
                                <template x-for="(slot, idx) in slots" :key="slot.key || idx">
                                    <div class="bg-yellow-50/50 p-3 rounded-xl border border-yellow-200/50">
                                        <label class="text-xs font-semibold text-gray-600 block mb-1">Demo <span x-text="slot.label || slot.key"></span></label>
                                        <input type="file" :name="slot.max > 1 ? `demo_media_files[${slot.key}][]` : `demo_media_files[${slot.key}]`" :multiple="slot.max > 1" :accept="slot.type === 'video' ? 'video/*' : 'image/*'" class="w-full border border-dashed border-yellow-200 rounded-lg px-2 py-1.5 text-xs focus:ring-1 focus:ring-yellow-400">
                                        <p class="text-[10px] text-yellow-600 mt-1" x-show="slot.max > 1">Bisa pilih hingga <span x-text="slot.max"></span> file sekaligus.</p>
                                        
                                        <!-- Show existing demo media for this slot -->
                                        <template x-if="editData?.parsed_demo_content?.media_files && editData.parsed_demo_content.media_files[slot.key]">
                                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                <template x-if="Array.isArray(editData.parsed_demo_content.media_files[slot.key])">
                                                    <template x-for="(fileUrl, fIdx) in editData.parsed_demo_content.media_files[slot.key]" :key="fIdx">
                                                        <a :href="'/storage/' + fileUrl" target="_blank" class="text-yellow-700 underline" x-text="`Lihat file ${fIdx + 1}`"></a>
                                                    </template>
                                                </template>
                                                <template x-if="!Array.isArray(editData.parsed_demo_content.media_files[slot.key])">
                                                    <a :href="'/storage/' + editData.parsed_demo_content.media_files[slot.key]" target="_blank" class="text-yellow-700 underline">Lihat file saat ini</a>
                                                </template>
                                                <label class="inline-flex items-center gap-1 text-[11px] text-gray-600 ml-2">
                                                    <input type="checkbox" :name="`demo_clear_media[${slot.key}]`" value="1" class="rounded border-gray-300 text-yellow-500 text-[10px]">
                                                    <span>Hapus</span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Bank & QRIS Demo -->
                        <div class="pt-2 border-t border-yellow-200/50">
                            <p class="text-sm font-semibold text-yellow-700 mb-2">Data Hadiah Digital Demo</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Demo Bank Name</label>
                                    <input type="text" name="demo_bank_name" placeholder="Misal: BCA" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.bank_accounts?.[0]?.bank_name ?? '') : @js(old('demo_bank_name'))">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Demo Rekening</label>
                                    <input type="text" name="demo_bank_account" placeholder="Misal: 12345678" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.bank_accounts?.[0]?.account_number ?? '') : @js(old('demo_bank_account'))">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="text-xs font-semibold text-gray-600">Demo Nama Penerima</label>
                                    <input type="text" name="demo_bank_owner" placeholder="Misal: Budi Santoso" class="mt-1 w-full border border-yellow-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400" :value="editData ? (editData.parsed_demo_content?.bank_accounts?.[0]?.account_name ?? '') : @js(old('demo_bank_owner'))">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Demo QRIS (Opsional)</label>
                                <input type="file" name="demo_qris" accept="image/*" class="mt-1 w-full border border-dashed border-yellow-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400">
                                <template x-if="editData?.parsed_demo_content?.qris_image">
                                    <div class="mt-1 flex items-center gap-3 text-xs text-yellow-700">
                                        <a :href="'/storage/' + editData.parsed_demo_content.qris_image" target="_blank" class="underline">Lihat QRIS saat ini</a>
                                        <label class="inline-flex items-center gap-1">
                                            <input type="checkbox" name="demo_clear_qris" value="1" class="rounded border-gray-300 text-yellow-500">
                                            <span>Hapus QRIS</span>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Harga (Rp)</label>
                        <input type="number" name="price" min="0" step="1" inputmode="numeric" :value="editData?.price ?? 0" class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Diskon (%)</label>
                        <input type="number" name="promo_discount_percent" min="0" max="100" step="0.1" :value="editData?.promo_discount_percent ?? 0" class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Label Promo</label>
                        <input type="text" name="promo_label" :value="editData?.promo_label" placeholder="Promo Spesial" class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Berakhir Pada</label>
                        <input type="date" name="promo_expires_at" :value="editData?.promo_expires_at ? editData.promo_expires_at.slice(0, 10) : ''" class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                </div>
                <textarea name="promo_description" rows="2" placeholder="Deskripsi promo (opsional)" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 resize-none" x-text="editData?.promo_description ?? @js(old('promo_description'))"></textarea>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Thumbnail Kartu</label>
                    <input type="file" name="thumbnail" accept="image/*" class="mt-1 w-full border border-dashed border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                    <p class="text-[11px] text-gray-400 mt-1">Gambar ini tampil di kartu daftar template (ratio 4:3 disarankan).</p>
                    <template x-if="editData?.thumbnail">
                        <a :href="editData?.thumbnail ? '{{ asset('storage') }}/' + editData.thumbnail : '#'" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-500 mt-1">
                            <i class="fas fa-image"></i> Lihat thumbnail saat ini
                        </a>
                    </template>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Urutan Tampil</label>
                    <input type="number" name="sort_order" :value="editData?.sort_order ?? 99" placeholder="Contoh: 1" min="1" class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <p class="text-[11px] text-gray-400 mt-1">Kotak ini untuk menentukan urutan tampil di daftar template (lebih kecil = tampil lebih atas).</p>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" :checked="editData?.is_active ?? true" class="rounded border-gray-300 text-yellow-500">
                        Template Aktif
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="hidden" name="is_premium" value="0">
                        <input type="checkbox" name="is_premium" value="1" :checked="editData?.is_premium ?? false" class="rounded border-purple-400 text-purple-600 focus:ring-purple-400">
                        Tandai Premium
                    </label>
                </div>
                
                <input type="hidden" name="media_slots" :value="JSON.stringify(slots)">
                <div class="border border-purple-100 bg-purple-50/60 rounded-2xl p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-purple-700">Slot Media Dinamis</p>
                            <p class="text-[11px] text-purple-600">Formulir user akan otomatis menyesuaikan slot media ini.</p>
                        </div>
                        <button type="button" @click="slots.push({ key: '', label: '', type: 'image', max: 1 })" class="text-xs bg-purple-100 text-purple-700 px-3 py-1.5 rounded-lg hover:bg-purple-200 font-semibold">+ Tambah Slot</button>
                    </div>
                    <template x-if="slots.length === 0">
                        <p class="text-xs text-gray-500 text-center py-2">Belum ada slot media. Tambahkan slot agar user bisa upload foto/video sesuai tema.</p>
                    </template>
                    <div class="space-y-3">
                        <template x-for="(slot, idx) in slots" :key="idx">
                            <div class="bg-white border text-xs border-purple-100 p-3 rounded-xl relative group">
                                <button type="button" @click="slots.splice(idx, 1)" class="absolute -top-2 -right-2 bg-red-100 text-red-600 w-6 h-6 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center shadow-sm"><i class="fas fa-times"></i></button>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="font-semibold text-gray-600 mb-1 block">Key (unik)</label>
                                        <input type="text" x-model="slot.key" placeholder="Contoh: gallery" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-purple-400 focus:border-purple-400">
                                    </div>
                                    <div>
                                        <label class="font-semibold text-gray-600 mb-1 block">Label Form</label>
                                        <input type="text" x-model="slot.label" placeholder="Contoh: Galeri Foto" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-purple-400 focus:border-purple-400">
                                    </div>
                                    <div>
                                        <label class="font-semibold text-gray-600 mb-1 block">Tipe</label>
                                        <select x-model="slot.type" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-purple-400 focus:border-purple-400">
                                            <option value="image">Gambar</option>
                                            <option value="video">Video</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="font-semibold text-gray-600 mb-1 block">Maks File</label>
                                        <input type="number" x-model="slot.max" min="1" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-purple-400 focus:border-purple-400">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="addOpen = false; editData = null" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 gold-gradient text-white font-semibold py-3 rounded-xl text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
