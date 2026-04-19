@extends('layouts.admin')
@section('title', 'Riwayat Aktivitas Admin')
@section('page-title', 'Riwayat Aktivitas Admin')
@section('breadcrumb', 'Riwayat Aktivitas')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="space-y-5"
     x-data="activityLogs({
        initialTable: @js(view('admin.activities.partials.table', ['activities' => $activities])->render()),
        initialSearch: @js(request('search'))
     })">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Filter Aktivitas</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm" x-ref="filterForm">
            <div>
                <label class="text-xs text-gray-500">Admin</label>
                <select name="user_id" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2">
                    <option value="">Semua Admin</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ request('user_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500">Method</label>
                <select name="method" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2">
                    <option value="">Semua</option>
                    @foreach(['GET','POST','PUT','PATCH','DELETE'] as $method)
                        <option value="{{ $method }}" {{ request('method') === $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs text-gray-500">Aksi / Route Name</label>
                <input type="text" name="action" value="{{ request('action') }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2" placeholder="cari berdasarkan aksi">
            </div>
            <div>
                <label class="text-xs text-gray-500">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="text-xs text-gray-500">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2">
            </div>
            <input type="hidden" name="search" :value="search">
            <div class="md:col-span-5 flex justify-end gap-3">
                <a href="{{ route('admin.activities.index') }}" class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">Reset</a>
                <button type="submit" class="px-4 py-2 gold-gradient text-white font-semibold rounded-xl">Terapkan Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Log Aktivitas</h3>
                <p class="text-xs text-gray-500">Log tidak dapat dihapus maupun diedit.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    <input type="search"
                           x-model="search"
                           placeholder="Pencarian realtime (admin, aksi, route...)"
                           class="pl-8 pr-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-yellow-400 w-72">
                </div>
                <button type="button"
                        @click="exportCsv"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-file-export text-yellow-500"></i> Ekspor CSV
                </button>
            </div>
        </div>
        <div class="relative" x-bind:class="fetching ? 'opacity-60 pointer-events-none' : ''">
            <div x-ref="tableContainer">
                @include('admin.activities.partials.table', ['activities' => $activities])
            </div>
            <div x-show="fetching" x-cloak class="absolute inset-0 flex items-center justify-center">
                <div class="text-xs text-gray-500 flex items-center gap-2 bg-white/80 px-4 py-2 rounded-xl border border-gray-200 shadow">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </div>
            </div>
        </div>
    </div>

    <div x-show="payloadModal" x-cloak class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Detail Payload</p>
                    <p class="text-xs text-gray-500">Data tersimpan seperti saat request dilakukan.</p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600" @click="payloadModal = null"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-5 max-h-[70vh] overflow-y-auto text-xs font-mono bg-gray-50 rounded-b-2xl">
                <template x-if="payloadModal">
                    <pre x-text="JSON.stringify(payloadModal, null, 2)"></pre>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function activityLogs({ initialTable, initialSearch }) {
        return {
            payloadModal: null,
            search: initialSearch || '',
            fetching: false,
            init() {
                this.$nextTick(() => {
                    if (this.$refs.tableContainer) {
                        this.$refs.tableContainer.innerHTML = initialTable;
                    }
                });

                this.$watch('search', Alpine.debounce(() => {
                    this.fetchActivities();
                }, 400));
            },
            openPayload(payload) {
                this.payloadModal = payload;
            },
            buildParams(extra = {}) {
                const formData = new FormData(this.$refs.filterForm);
                const params = new URLSearchParams();
                formData.forEach((value, key) => {
                    if (value !== '') params.append(key, value);
                });
                if (this.search) {
                    params.set('search', this.search);
                } else {
                    params.delete('search');
                }
                Object.entries(extra).forEach(([key, value]) => {
                    params.set(key, value);
                });

                return params.toString();
            },
            fetchActivities(customUrl = null) {
                this.fetching = true;
                const base = customUrl ?? `{{ route('admin.activities.index') }}?${this.buildParams({ partial: 1 })}`;
                fetch(base, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        if (this.$refs.tableContainer) {
                            this.$refs.tableContainer.innerHTML = html;
                        }
                    })
                    .finally(() => {
                        this.fetching = false;
                    });
            },
            exportCsv() {
                const url = `{{ route('admin.activities.export') }}?${this.buildParams({})}`;
                window.open(url, '_blank');
            },
            openPayload(payload) {
                this.payloadModal = payload;
            }
        }
    }
</script>
@endpush
