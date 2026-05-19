@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .dark .apexcharts-text { fill: #94a3b8 !important; transition: fill 0.3s ease; }
    .dark .apexcharts-legend-text { color: #94a3b8 !important; transition: color 0.3s ease; }
    .dark .apexcharts-tooltip { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.5); }
    .dark .apexcharts-tooltip-title { background: #0f172a !important; border-bottom-color: #334155 !important; font-weight: bold; }
    .dark .apexcharts-gridline { stroke: #334155 !important; transition: stroke 0.3s ease; }
    
    /* Mencegah tumpahan scrollbar pada wrapper grafik */
    .chart-wrapper { width: 100%; overflow: hidden !important; }
</style>

<div class="max-w-7xl mx-auto space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white transition-colors">{{ __('dashboard') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors">Ringkasan kondisi finansial dan piutang usaha per hari ini.</p>
        </div>
        <div class="w-full sm:w-auto">
            <a href="{{ route('receivables.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl hover:bg-emerald-200 dark:hover:bg-emerald-900/80 transition-all cursor-pointer shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                Tambah Piutang
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/50 transition-colors">
            <div class="flex items-center">
                <div class="p-1.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg mr-3 shrink-0 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" /></svg>
                </div>
                <h2 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider transition-colors">Notifikasi Peringatan Tagihan</h2>
            </div>
            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1.5 rounded-lg sm:bg-transparent sm:px-0 sm:py-0 self-start sm:self-auto">Lihat Semua Arsip &rarr;</a>
        </div>
        
        <div class="p-4 space-y-2.5">
            @if($urgentAlerts->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-gray-300 dark:text-slate-600 mb-2 transition-colors"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium transition-colors">Tidak ada notifikasi baru.</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 transition-colors">Semua pembukuan piutang berjalan dengan aman dan terkendali.</p>
                </div>
            @else
                @foreach($urgentAlerts as $notif)
                    @php
                        $days = 0;
                        if(isset($notif->data['due_date'])) {
                            $dueDate = \Carbon\Carbon::parse($notif->data['due_date'])->startOfDay();
                            $today = \Carbon\Carbon::today()->startOfDay();
                            $days = (int) abs($today->diffInDays($dueDate, false));
                        }
                    @endphp
                    <div class="flex items-start p-3.5 sm:p-4 rounded-xl transition-colors {{ $notif->data['type'] === 'overdue' ? 'bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800/30' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30' }}">
                        <div class="shrink-0 mt-0.5 mr-3">
                            @if($notif->data['type'] === 'overdue')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-rose-500 dark:text-rose-400"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-amber-500 dark:text-amber-400"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold leading-relaxed {{ $notif->data['type'] === 'overdue' ? 'text-rose-800 dark:text-rose-300' : 'text-amber-800 dark:text-amber-300' }}">
                                {{ $notif->data['type'] === 'overdue' ? __('notif_overdue', ['name' => $notif->data['customer_name'] ?? '', 'days' => $days]) : __('notif_due_soon', ['name' => $notif->data['customer_name'] ?? '', 'days' => $days]) }}
                            </p>
                            <p class="text-[11px] sm:text-xs mt-0.5 opacity-80 {{ $notif->data['type'] === 'overdue' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-700 dark:text-amber-400' }}">
                                Harap segera hubungi pelanggan yang bersangkutan untuk penagihan aman berkala.
                            </p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-3">
        
        <div class="relative bg-white dark:bg-slate-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-5 sm:p-6 group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-xl shadow-inner shrink-0 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-400 dark:text-slate-400 uppercase tracking-wider transition-colors">{{ __('active_receivables') }}</p>
                    <p class="text-lg sm:text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight truncate transition-colors">Rp {{ number_format($activeAmount, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1.5 text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-400 transition-colors">
                        <span class="px-1.5 py-0.5 rounded-md bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 mr-1.5 font-bold">{{ $activeCount }}</span> Transaksi berjalan
                    </div>
                </div>
            </div>
        </div>

        <div class="relative bg-white dark:bg-slate-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-5 sm:p-6 group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 dark:bg-amber-900/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl shadow-inner shrink-0 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-400 dark:text-slate-400 uppercase tracking-wider transition-colors">{{ __('due_soon') }}</p>
                    <p class="text-lg sm:text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight truncate transition-colors">Rp {{ number_format($dueSoonAmount, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1.5 text-[11px] sm:text-xs font-medium text-amber-600 dark:text-amber-400 transition-colors">
                        <span class="px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/50 mr-1.5 font-bold">{{ $dueSoonCount }}</span> Perlu ditagih
                    </div>
                </div>
            </div>
        </div>

        <div class="relative bg-white dark:bg-slate-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-5 sm:p-6 group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start space-x-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl shadow-inner shrink-0 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-400 dark:text-slate-400 uppercase tracking-wider transition-colors">{{ __('overdue') }}</p>
                    <p class="text-lg sm:text-2xl font-black text-rose-600 dark:text-rose-500 mt-1 tracking-tight truncate transition-colors">Rp {{ number_format($overdueAmount, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1.5 text-[11px] sm:text-xs font-bold text-rose-600 dark:text-rose-400 transition-colors">
                        <span class="px-1.5 py-0.5 rounded-md bg-rose-100 dark:bg-rose-900/50 mr-1.5 font-bold">{{ $overdueCount }}</span> Lewat batas waktu!
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 transition-colors duration-300 chart-wrapper">
            <h2 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider mb-4 transition-colors">Tren Transaksi Piutang (6 Bulan)</h2>
            <div id="trendChart" class="w-full"></div>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 transition-colors duration-300 chart-wrapper">
            <h2 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider mb-4 transition-colors">Top 5 Pelanggan (Piutang Terbesar)</h2>
            <div id="topCustomerChart" class="w-full"></div>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 transition-colors duration-300 chart-wrapper">
            <h2 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider mb-4 transition-colors">Rincian Piutang Aktif</h2>
            <div id="komposisiChart" class="w-full flex justify-center"></div>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 transition-colors duration-300 chart-wrapper">
            <h2 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider mb-4 transition-colors">Rasio Lunas vs Belum Lunas</h2>
            <div id="statusChart" class="w-full flex justify-center"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // --- GRAFIK 1: TREN PIUTANG (AREA) ---
        var trendMonths = {!! json_encode($trendMonths) !!};
        var trendTotals = {!! json_encode($trendTotals) !!};
        
        var trendOptions = {
            series: [{ name: 'Total Pencatatan', data: trendTotals }],
            chart: { type: 'area', height: 320, width: '100%', fontFamily: 'inherit', toolbar: { show: false }, background: 'transparent' },
            colors: ['#0ea5e9'], 
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { 
                categories: trendMonths, 
                tooltip: { enabled: false },
                labels: { style: { fontSize: '11px' }, hideOverlappingLabels: true }
            },
            yaxis: {
                labels: {
                    formatter: function (value) { return "Rp " + (value / 1000000).toFixed(1) + " Jt"; },
                    style: { fontSize: '11px' }
                }
            },
            tooltip: {
                y: { formatter: function(value) { return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); } }
            },
            responsive: [{
                breakpoint: 480,
                options: { chart: { height: 280 } }
            }]
        };
        new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

        // --- 🟢 GRAFIK 2: TOP PELANGGAN (BAR HORIZONTAL) - FIXED SCROLLBAR MOBILE ---
        var topCustomerNames = {!! json_encode($topCustomerNames) !!};
        var topCustomerTotals = {!! json_encode($topCustomerTotals) !!};

        var topOptions = {
            series: [{ name: 'Sisa Utang', data: topCustomerTotals }],
            chart: { 
                type: 'bar', 
                height: 320, 
                width: '100%', // Kunci utama mencegah melebar keluar layar
                fontFamily: 'inherit', 
                toolbar: { show: false }, 
                background: 'transparent' 
            },
            colors: ['#8b5cf6'], 
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: topCustomerNames,
                labels: { 
                    formatter: function (value) { return "Rp " + (value / 1000000).toFixed(1) + " Jt"; },
                    hideOverlappingLabels: true, // Mencegah tulisan harga bertumpuk
                    style: { fontSize: '11px' }
                }
            },
            yaxis: {
                labels: {
                    show: true,
                    maxWidth: 120, // Kunci utama mencegah nama kepanjangan merusak layout
                    style: { fontSize: '11px' }
                }
            },
            tooltip: {
                y: { formatter: function(value) { return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); } }
            },
            responsive: [{
                breakpoint: 480, // Pengaturan Khusus Layar HP
                options: {
                    chart: { height: 300 },
                    yaxis: { 
                        labels: { 
                            maxWidth: 90, // Memperpendek nama jika layar sangat sempit
                            style: { fontSize: '10px' } 
                        } 
                    },
                    xaxis: { 
                        labels: { style: { fontSize: '9px' } } 
                    }
                }
            }]
        };
        new ApexCharts(document.querySelector("#topCustomerChart"), topOptions).render();

        // --- GRAFIK 3: KOMPOSISI AKTIF (DONUT RESPONSIF) ---
        var active = {{ $activeAmount ?? 0 }};
        var dueSoon = {{ $dueSoonAmount ?? 0 }};
        var overdue = {{ $overdueAmount ?? 0 }};

        var komposisiOptions = {
            series: [active, dueSoon, overdue],
            chart: { type: 'donut', height: 320, width: '100%', fontFamily: 'inherit', background: 'transparent' },
            labels: ['Transaksi Berjalan', 'Perlu Ditagih', 'Lewat Batas Waktu'],
            colors: ['#3b82f6', '#f59e0b', '#f43f5e'],
            plotOptions: { pie: { donut: { size: '75%' } } },
            dataLabels: { enabled: false },
            stroke: { width: 4, colors: ['transparent'] },
            legend: { position: 'bottom', markers: { radius: 12 }, itemMargin: { horizontal: 10, vertical: 5 } },
            tooltip: { y: { formatter: function(value) { return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); } } },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 280 },
                    legend: { position: 'bottom', fontSize: '11px', itemMargin: { horizontal: 5, vertical: 2 } }
                }
            }]
        };
        new ApexCharts(document.querySelector("#komposisiChart"), komposisiOptions).render();

        // --- GRAFIK 4: LUNAS VS BELUM LUNAS (PIE RESPONSIF) ---
        var paidAmount = {{ $paidAmount ?? 0 }};
        var unpaidAmount = {{ $unpaidAmount ?? 0 }};

        var statusOptions = {
            series: [paidAmount, unpaidAmount],
            chart: { type: 'pie', height: 320, width: '100%', fontFamily: 'inherit', background: 'transparent' },
            labels: ['Sudah Lunas', 'Belum Lunas'],
            colors: ['#10b981', '#f43f5e'], 
            dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 'bold' }, dropShadow: { enabled: true } },
            stroke: { width: 4, colors: ['transparent'] },
            legend: { position: 'bottom', markers: { radius: 12 }, itemMargin: { horizontal: 10, vertical: 5 } },
            tooltip: { y: { formatter: function(value) { return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); } } },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 280 },
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { style: { fontSize: '11px' } }
                }
            }]
        };
        new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();
    });
</script>
@endsection