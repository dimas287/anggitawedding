@php use Illuminate\Support\Str; @endphp

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3 text-left">Waktu</th>
                <th class="px-4 py-3 text-left">Admin</th>
                <th class="px-4 py-3 text-left">Aksi</th>
                <th class="px-4 py-3 text-left">Route / URL</th>
                <th class="px-4 py-3 text-left">Method</th>
                <th class="px-4 py-3 text-left">IP</th>
                <th class="px-4 py-3 text-left">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($activities as $activity)
                <tr class="hover:bg-gray-50 {{ $activity->is_critical ? 'bg-red-50/50' : '' }}">
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">{{ $activity->created_at?->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $activity->user->name ?? 'System' }}</div>
                        <div class="text-xs text-gray-500">ID {{ $activity->user_id ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <div class="font-semibold text-gray-800 flex items-center gap-2">
                                {{ $activity->action }}
                                @if($activity->is_critical)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                        {{ $activity->critical_label }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">{{ Str::limit($activity->payload ? json_encode($activity->payload) : '-', 80) }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-700">{{ $activity->route ?? '-' }}</div>
                        <div class="text-xs text-blue-600 truncate max-w-xs">{{ $activity->url }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ match($activity->method){ 'POST'=>'bg-blue-100 text-blue-700', 'PUT'=>'bg-yellow-100 text-yellow-700', 'PATCH'=>'bg-amber-100 text-amber-700', 'DELETE'=>'bg-red-100 text-red-600', default=>'bg-gray-100 text-gray-600'} }}">{{ $activity->method }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <div>{{ $activity->ip_address }}</div>
                        <div class="text-[11px] text-gray-400">{{ Str::limit($activity->user_agent, 40) }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <button type="button" class="text-xs text-amber-600 font-semibold hover:text-amber-800" @click="openPayload(@js($activity->payload ?? (object) []))">Lihat Payload</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada aktivitas admin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="px-4 py-3 border-t" data-pagination>
    {{ $activities->appends(request()->except(['page', 'partial']))->links() }}
</div>
