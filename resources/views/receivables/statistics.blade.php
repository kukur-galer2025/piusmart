@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .dark .apexcharts-text { fill: #cbd5e1 !important; }
    .dark .apexcharts-text.apexcharts-title-text { fill: #f1f5f9 !important; }
    .dark .apexcharts-legend-text { color: #cbd5e1 !important; }
    .dark .apexcharts-tooltip { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .apexcharts-tooltip-title { background: #0f172a !important; border-bottom-color: #334155 !important; font-weight: bold; }
    .dark .apexcharts-gridline { stroke: #334155 !important; }
    .dark .apexcharts-menu { background: #1e293b !important; border-color: #334155 !important; color: #e2e8f0 !important; }
    .dark .apexcharts-theme-light .apexcharts-menu-item:hover { background: #334155 !important; }
    .dark .apexcharts-datalabel-label { fill: #e2e8f0 !important; }
    .dark .apexcharts-datalabel-value { fill: #f8fafc !important; }
    .dark .apexcharts-pie-label { fill: #ffffff !important; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.5)); }
    .chart-card { width: 100%; overflow: hidden !important; }
    .apexcharts-menu-icon { transform: scale(1.15); }
    .apexcharts-menu-icon svg { fill: #94a3b8 !important; }
</style>

<div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('statistics') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('statistics_subtitle') }}</p>
        </div>
        <a href="{{ route('receivables.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 shadow-sm cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
            {{ __('stat_back_to_list') }}
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <form method="GET" action="{{ route('receivables.statistics') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">{{ __('stat_filter_year') }}</label>
                <select name="year" class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('stat_all_years') }}</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">{{ __('stat_filter_month') }}</label>
                <select name="month" class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('stat_all_months') }}</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $filterMonth == sprintf('%02d', $m) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 dark:bg-indigo-500 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 shadow-sm cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 inline mr-1 -mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" /></svg>
                    Filter
                </button>
                @if($filterYear || $filterMonth)
                <a href="{{ route('receivables.statistics') }}" class="px-3 py-2.5 text-sm font-semibold text-gray-600 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 cursor-pointer">✕</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Filter Badge --}}
    @if($filterYear || $filterMonth)
    <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-semibold border border-indigo-100 dark:border-indigo-800/50">
        📊 {{ __('stat_period') }}:
        @if($filterMonth) {{ \Carbon\Carbon::create()->month((int)$filterMonth)->translatedFormat('F') }} @endif
        @if($filterYear) {{ $filterYear }} @endif
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php $cards = [
            ['stat_total_receivable', $totalAmount, $totalCount, 'blue', 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ['stat_total_paid', $paidAmount, $paidCount, 'emerald', 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ['stat_total_unpaid', $unpaidAmount, $unpaidCount, 'amber', 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ['stat_total_overdue', $overdueAmount, $overdueCount, 'rose', 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
        ]; @endphp
        @foreach($cards as [$label, $amount, $count, $color, $icon])
        <div class="bg-white dark:bg-slate-800 p-4 sm:p-5 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <div class="absolute right-0 top-0 w-16 h-16 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 rounded-bl-full -mr-3 -mt-3 transition-transform group-hover:scale-110"></div>
            <div class="p-2 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/40 text-{{ $color }}-600 dark:text-{{ $color }}-400 rounded-lg w-fit mb-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
            </div>
            <p class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">{{ __($label) }}</p>
            <p class="text-base sm:text-xl font-black text-gray-900 dark:text-white mt-1.5 truncate">Rp {{ number_format($amount, 0, ',', '.') }}</p>
            <p class="text-[10px] sm:text-xs font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-1.5">{{ $count }} {{ __('stat_transactions') }}</p>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-5 chart-card">
            <div id="chart-trend"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-5 chart-card">
            <div id="chart-monthly-paid"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-5 chart-card lg:col-span-2">
            <div id="chart-top"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-5 chart-card">
            <div id="chart-status"></div>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-5 chart-card">
            <div id="chart-pie"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Helper: Format Rupiah
    function fmtRp(v) { return 'Rp ' + v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function fmtJt(v) { return 'Rp ' + (v/1000000).toFixed(1) + ' Jt'; }

    // Shared toolbar config for HD export with title
    function toolbar(title) {
        return {
            show: true,
            tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false },
            export: {
                csv: {
                    filename: title,
                    headerCategory: 'Kategori',
                    headerValue: 'Nilai'
                },
                svg: { filename: title },
                png: { filename: title }
            }
        };
    }

    // Shared responsive
    var mobileResp = [{ breakpoint: 640, options: { chart: { height: 260 }, title: { style: { fontSize: '13px' } } } }];

    // ===================== CHART 1: Tren Piutang =====================
    new ApexCharts(document.querySelector('#chart-trend'), {
        series: [
            { name: '{{ __("stat_total_receivable") }}', data: {!! json_encode($trendNewAmounts) !!} },
            { name: '{{ __("stat_total_paid") }}', data: {!! json_encode($trendPaidAmounts) !!} }
        ],
        chart: { type: 'area', height: 340, fontFamily: 'inherit', background: 'transparent', toolbar: toolbar('{{ __("stat_chart_trend") }}') },
        title: { text: '{{ __("stat_chart_trend") }}', align: 'left', style: { fontSize: '15px', fontWeight: 700 } },
        colors: ['#6366f1', '#10b981'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        xaxis: { categories: {!! json_encode($trendMonths) !!}, labels: { style: { fontSize: '10px' }, hideOverlappingLabels: true, rotate: -45, rotateAlways: false } },
        yaxis: { labels: { formatter: fmtJt, style: { fontSize: '11px' } } },
        tooltip: { y: { formatter: fmtRp } },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
        responsive: mobileResp
    }).render();

    // ===================== CHART 2: Realisasi Pelunasan =====================
    new ApexCharts(document.querySelector('#chart-monthly-paid'), {
        series: [{ name: '{{ __("stat_total_paid") }}', data: {!! json_encode($paidMonthlyAmounts) !!} }],
        chart: { type: 'bar', height: 340, fontFamily: 'inherit', background: 'transparent', toolbar: toolbar('{{ __("stat_chart_monthly_paid") }}') },
        title: { text: '{{ __("stat_chart_monthly_paid") }}', align: 'left', style: { fontSize: '15px', fontWeight: 700 } },
        colors: ['#10b981'],
        plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: {!! json_encode($paidMonths) !!}, labels: { style: { fontSize: '10px' }, hideOverlappingLabels: true, rotate: -45, rotateAlways: false } },
        yaxis: { labels: { formatter: fmtJt, style: { fontSize: '11px' } } },
        tooltip: { y: { formatter: fmtRp } },
        responsive: mobileResp
    }).render();

    // ===================== CHART 3: Top 5 Pelanggan =====================
    new ApexCharts(document.querySelector('#chart-top'), {
        series: [{ name: '{{ __("stat_total_unpaid") }}', data: {!! json_encode($topTotals) !!} }],
        chart: { type: 'bar', height: 340, fontFamily: 'inherit', background: 'transparent', toolbar: toolbar('{{ __("stat_chart_top") }}') },
        title: { text: '{{ __("stat_chart_top") }}', align: 'left', style: { fontSize: '15px', fontWeight: 700 } },
        colors: ['#f59e0b'],
        plotOptions: { bar: { borderRadius: 5, horizontal: true, barHeight: '60%' } },
        dataLabels: { enabled: true, formatter: fmtRp, style: { fontSize: '11px' }, offsetX: 5 },
        xaxis: { categories: {!! json_encode($topNames) !!}, labels: { formatter: fmtJt, style: { fontSize: '10px' } } },
        yaxis: { labels: { maxWidth: 140, style: { fontSize: '12px', fontWeight: 600 } } },
        tooltip: { y: { formatter: fmtRp } },
        responsive: [{ breakpoint: 640, options: { chart: { height: 280 }, yaxis: { labels: { maxWidth: 100, style: { fontSize: '10px' } } }, dataLabels: { enabled: false }, title: { style: { fontSize: '13px' } } } }]
    }).render();

    // ===================== CHART 4: Komposisi Status (Donut) =====================
    new ApexCharts(document.querySelector('#chart-status'), {
        series: [{{ $statusPaid }}, {{ $statusUnpaid }}, {{ $statusDueSoon }}, {{ $statusOverdue }}],
        chart: { type: 'donut', height: 340, fontFamily: 'inherit', background: 'transparent', toolbar: toolbar('{{ __("stat_chart_status") }}') },
        title: { text: '{{ __("stat_chart_status") }}', align: 'left', style: { fontSize: '15px', fontWeight: 700 } },
        labels: ['{{ __("paid") }}', '{{ __("unpaid") }}', '{{ __("due_soon") }}', '{{ __("overdue") }}'],
        colors: ['#10b981', '#38bdf8', '#f59e0b', '#f43f5e'],
        plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, formatter: function(w) { return w.globals.seriesTotals.reduce((a,b) => a+b, 0) + ' data'; } } } } } },
        dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(1) + '%'; }, style: { fontSize: '11px', fontWeight: 600 } },
        stroke: { width: 3, colors: ['transparent'] },
        legend: { position: 'bottom', fontSize: '12px', markers: { radius: 12 }, itemMargin: { horizontal: 8, vertical: 4 } },
        tooltip: { y: { formatter: function(v) { return v + ' {{ __("stat_transactions") }}'; } } },
        responsive: [{ breakpoint: 640, options: { chart: { height: 300 }, legend: { fontSize: '11px' }, title: { style: { fontSize: '13px' } } } }]
    }).render();

    // ===================== CHART 5: Lunas vs Belum Lunas (Pie) =====================
    new ApexCharts(document.querySelector('#chart-pie'), {
        series: [{{ $paidAmount }}, {{ $unpaidAmount }}],
        chart: { type: 'pie', height: 340, fontFamily: 'inherit', background: 'transparent', toolbar: toolbar('{{ __("stat_chart_paid_vs_unpaid") }}') },
        title: { text: '{{ __("stat_chart_paid_vs_unpaid") }}', align: 'left', style: { fontSize: '15px', fontWeight: 700 } },
        labels: ['{{ __("paid") }}', '{{ __("unpaid") }}'],
        colors: ['#10b981', '#f43f5e'],
        dataLabels: { enabled: true, style: { fontSize: '13px', fontWeight: 'bold' }, dropShadow: { enabled: true, top: 1, left: 1, blur: 2, opacity: 0.3 } },
        stroke: { width: 3, colors: ['transparent'] },
        legend: { position: 'bottom', fontSize: '12px', markers: { radius: 12 }, itemMargin: { horizontal: 8, vertical: 4 } },
        tooltip: { y: { formatter: fmtRp } },
        responsive: [{ breakpoint: 640, options: { chart: { height: 300 }, legend: { fontSize: '11px' }, title: { style: { fontSize: '13px' } } } }]
    }).render();
});
</script>
@endsection
