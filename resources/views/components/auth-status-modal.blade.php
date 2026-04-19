@php
    $validationErrors = [];
    if (isset($errors) && $errors->any()) {
        $validationErrors = $errors->all();
    }
    $statusPayload = session()->only(['auth_status', 'auth_status_type', 'auth_status_headline', 'success', 'error']);
    if (!empty($validationErrors)) {
        $statusPayload['validation_errors'] = $validationErrors;
    }
@endphp

<div x-data='authNotiComponent(@json($statusPayload))' x-init="init()">
    <template x-if="visible">
        <div @keydown.escape.window="close()"
             class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-10"
             x-show="visible" x-transition.opacity>
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()" x-transition.opacity></div>
            <div class="relative bg-white rounded-3xl shadow-[0_25px_80px_rgba(15,23,42,0.45)] w-full max-w-md overflow-hidden"
                 x-transition.scale.origin-bottom duration-300>
                <div class="h-1 w-full" :class="type === 'success' ? 'bg-gradient-to-r from-emerald-400 via-green-500 to-emerald-500' : 'bg-gradient-to-r from-rose-400 via-red-500 to-rose-500'"></div>
                <div class="p-8 text-center space-y-4">
                    <div class="mx-auto w-16 h-16 rounded-2xl flex items-center justify-center"
                         :class="type === 'success' ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500'">
                        <i :class="type === 'success' ? 'fas fa-circle-check text-2xl' : 'fas fa-circle-xmark text-2xl'"></i>
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-[0.35em] text-gray-400" x-text="label"></p>
                        <h3 class="text-2xl font-semibold text-gray-900" x-text="headline"></h3>
                    </div>
                    <!-- Handle HTML inner messages properly for lists -->
                    <div class="text-gray-600 text-sm leading-relaxed text-left inline-block" x-html="message"></div>
                    <div class="pt-2">
                        <button @click="close()"
                                class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-sm font-semibold"
                                :class="type === 'success' ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-rose-500 text-white hover:bg-rose-600'">
                            <i class="fas fa-check"></i> Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
if (typeof window.authNotiComponent === 'undefined') {
    window.authNotiComponent = function authNotiComponent(payload = null) {
        return {
            payload,
            visible: false,
            type: 'success',
            headline: '',
            message: '',
            label: 'STATUS',
            init() {
                window.AnggitaStatusModal = { show: (data) => this.open(data) };
                if (!this.payload) return;
                if (this.payload.auth_status) {
                    this.open({
                        type: this.payload.auth_status_type || 'success',
                        headline: this.payload.auth_status_headline,
                        message: this.payload.auth_status,
                    });
                    return;
                }
                if (this.payload.validation_errors) {
                    this.open({
                        type: 'error',
                        headline: 'Proses Gagal',
                        message: '<ul class="list-disc ml-5 space-y-1">' + this.payload.validation_errors.map(e => '<li>' + e + '</li>').join('') + '</ul>',
                    });
                    return;
                }
                if (this.payload.success || this.payload.error) {
                    this.open({
                        type: this.payload.success ? 'success' : 'error',
                        message: this.payload.success || this.payload.error,
                    });
                }
            },
            open(data) {
                this.type = data?.type === 'error' ? 'error' : 'success';
                this.headline = data?.headline || (this.type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan');
                this.message = data?.message || '';
                this.label = (this.type === 'success' ? 'BERHASIL' : 'GAGAL');
                this.visible = true;
                this.autoClose();
            },
            close() { this.visible = false; },
            autoClose() {
                setTimeout(() => { if (this.visible) this.close(); }, 7000);
            }
        };
    }
}
</script>
