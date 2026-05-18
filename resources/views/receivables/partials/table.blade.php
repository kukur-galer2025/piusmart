<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200 tracking-wider font-semibold whitespace-nowrap">
            <tr>
                <th class="px-6 py-4">{{ __('customer_name') }}</th>
                <th class="px-6 py-4">{{ __('amount') }}</th>
                <th class="px-6 py-4">{{ __('transaction_date') }}</th>
                <th class="px-6 py-4">{{ __('due_date') }}</th>
                <th class="px-6 py-4 text-center">{{ __('status') }}</th>
                <th class="px-6 py-4 text-center">{{ __('action') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($receivables as $item)
                @php
                    $today = \Carbon\Carbon::today();
                    $dueDate = \Carbon\Carbon::parse($item->due_date)->startOfDay();
                    
                    if ($item->is_paid) {
                        $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        $statusLabel = __('paid');
                    } elseif ($today->gt($dueDate)) {
                        $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                        $statusLabel = __('overdue');
                    } elseif ($today->diffInDays($dueDate, false) <= 3 && $today->diffInDays($dueDate, false) >= 0) {
                        $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                        $statusLabel = __('due_soon');
                    } else {
                        $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                        $statusLabel = __('unpaid');
                    }
                @endphp
                <tr class="hover:bg-gray-50/70 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-gray-900">{{ $item->customer->name }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">📞 {{ $item->customer->phone ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                        {{ $item->transaction_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                        {{ $item->due_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            @if(!$item->is_paid)
                                <button title="Tandai Lunas" @click="openModal = true; actionUrl = '{{ route('receivables.mark-as-paid', $item->id) }}'; customerName = '{{ $item->customer->name }}'" 
                                        class="p-1.5 text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-600 hover:text-white border border-emerald-200 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </button>
                            @endif
                            <a href="{{ route('receivables.edit', $item->id) }}" title="Edit Data" class="p-1.5 text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-600 hover:text-white border border-amber-200 transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg>
                            </a>
                            <button title="Hapus Data" @click="openDeleteModal = true; deleteUrl = '{{ route('receivables.destroy', $item->id) }}'; customerName = '{{ $item->customer->name }}'" class="p-1.5 text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-600 hover:text-white border border-rose-200 transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">Tidak ada data piutang ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($receivables->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $receivables->links() }}
    </div>
@endif