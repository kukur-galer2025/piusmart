@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('dashboard') }}</h1>
        <p class="text-sm text-gray-500 mt-1">Ringkasan kondisi piutang usaha per hari ini.</p>
    </div>

    <!-- ========================================================================= -->
    <!-- STATS CARDS (Metrik Utama) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Card: Piutang Aktif -->
        <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-gray-200 p-6 flex items-center space-x-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl text-2xl">🔹</div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('active_receivables') }}</p>
                <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($activeAmount, 0, ',', '.') }}</p>
                <span class="text-xs text-gray-500 font-medium">{{ $activeCount }} transaksi belum lunas</span>
            </div>
        </div>

        <!-- Card: Akan Jatuh Tempo -->
        <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-gray-200 p-6 flex items-center space-x-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl text-2xl">⚠️</div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('due_soon') }} (H-3)</p>
                <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($dueSoonAmount, 0, ',', '.') }}</p>
                <span class="text-xs text-amber-600 font-semibold">{{ $dueSoonCount }} mendesak</span>
            </div>
        </div>

        <!-- Card: Terlambat -->
        <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-gray-200 p-6 flex items-center space-x-4">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl text-2xl">🚨</div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('overdue') }}</p>
                <p class="text-xl font-bold text-rose-600 mt-1">Rp {{ number_format($overdueAmount, 0, ',', '.') }}</p>
                <span class="text-xs text-rose-500 font-medium font-semibold">{{ $overdueCount }} lewat jatuh tempo</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- NOTIFICATION SYSTEM PANEL -->
    <!-- ========================================================================= -->
    <div class="bg-white shadow-xs rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center">
            <span class="text-lg mr-2">🔔</span>
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">{{ __('notification') }}</h2>
        </div>
        <div class="p-6 divide-y divide-gray-100">
            @if($notifications->isEmpty())
                <p class="text-sm text-gray-500 text-center py-4">{{ __('no_notification') }}</p>
            @else
                @foreach($notifications as $notif)
                    <div class="flex items-start py-3.5 first:pt-0 last:pb-0 space-x-3">
                        <span class="mt-0.5 text-base">
                            {{ $notif['type'] === 'overdue' ? '🔴' : '🟡' }}
                        </span>
                        <p class="text-sm font-medium {{ $notif['type'] === 'overdue' ? 'text-rose-700' : 'text-gray-700' }}">
                            {{ $notif['message'] }}
                        </p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection