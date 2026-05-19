@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .dark .apexcharts-text { fill: #94a3b8 !important; }
    .dark .apexcharts-legend-text { color: #94a3b8 !important; }
    .dark .apexcharts-tooltip { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.5); }
    .dark .apexcharts-tooltip-title { background: #0f172a !important; border-bottom-color: #334155 !important; font-weight: bold; }
    .dark .apexcharts-gridline { stroke: #334155 !important; }
    .dark .apexcharts-menu { background: #1e293b !important; border-color: #334155 !important; color: #e2e8f0 !important; }
    .dark .apexcharts-theme-light .apexcharts-menu-item:hover { background: #334155 !important; }
    .chart-card { width: 100%; overflow: hidden !important; }
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
        <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <div class="absolute right-0 top-0 w-10 h-10 sm:w-14 sm:h-14 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 rounded-bl-full -mr-2 -mt-2 transition-transform group-hover:scale-110"></div>
            <div class="p-1.5 sm:p-2 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/40 text-{{ $color }}-600 dark:text-{{ $color }}-400 rounded-md sm:rounded-lg w-fit mb-1.5 sm:mb-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 sm:w-5 sm:h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
            </div>
            <p class="text-[9px] sm:text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider leading-tight">{{ __($label) }}</p>
            <p class="text-sm sm:text-xl font-black text-gray-900 dark:text-white mt-1 sm:mt-1.5 truncate">Rp {{ number_format($amount, 0, ',', '.') }}</p>
            <p class="text-[9px] sm:text-xs font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-1 sm:mt-1.5">{{ $count }} {{ __('stat_transactions') }}</p>
        </div>
        @endforeach
    </div>

    {{-- Charts: JUDUL PAKAI HTML, BUKAN ApexCharts title --}}
    <div class="space-y-5 sm:space-y-6">

        {{-- Row 1: Tren + Pelunasan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 chart-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">📈 {{ __('stat_chart_trend') }}</h3>
                    <button onclick="downloadChart('trend', '{{ __('stat_chart_trend') }}')" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors cursor-pointer" title="Download PNG">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    </button>
                </div>
                <div id="chart-trend" class="w-full"></div>
            </div>
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 chart-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">💰 {{ __('stat_chart_monthly_paid') }}</h3>
                    <button onclick="downloadChart('monthly-paid', '{{ __('stat_chart_monthly_paid') }}')" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors cursor-pointer" title="Download PNG">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    </button>
                </div>
                <div id="chart-monthly-paid" class="w-full"></div>
            </div>
        </div>

        {{-- Row 2: Top 5 --}}
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 chart-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">🏆 {{ __('stat_chart_top') }}</h3>
                <button onclick="downloadChart('top', '{{ __('stat_chart_top') }}')" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors cursor-pointer" title="Download PNG">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                </button>
            </div>
            <div id="chart-top" class="w-full"></div>
        </div>

        {{-- Row 3: Donut + Pie --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 chart-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">🍩 {{ __('stat_chart_status') }}</h3>
                    <button onclick="downloadChart('status', '{{ __('stat_chart_status') }}')" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors cursor-pointer" title="Download PNG">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    </button>
                </div>
                <div id="chart-status" class="w-full flex justify-center"></div>
            </div>
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-gray-100 dark:border-slate-700 p-4 sm:p-6 chart-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">🥧 {{ __('stat_chart_paid_vs_unpaid') }}</h3>
                    <button onclick="downloadChart('pie', '{{ __('stat_chart_paid_vs_unpaid') }}')" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors cursor-pointer" title="Download PNG">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    </button>
                </div>
                <div id="chart-pie" class="w-full flex justify-center"></div>
            </div>
        </div>

    </div>
</div>

<script>
    // Simpan instance chart secara global untuk fungsi download
    var chartInstances = {};

    function downloadChart(id, title) {
        var chart = chartInstances[id];
        if (!chart) return;
        chart.dataURI({ scale: 2 }).then(function(res) {
            var link = document.createElement('a');
            link.href = res.imgURI;
            link.download = title + '.png';
            link.click();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {

        function fmtRp(v) { return 'Rp ' + v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
        function fmtJt(v) { return 'Rp ' + (v / 1000000).toFixed(1) + ' Jt'; }

        function getChartTheme() {
            return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        }

        function getChartBackground() {
            return document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff';
        }
        
        // Listen for dark mode changes to update charts
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === "class") {
                    const themeMode = getChartTheme();
                    const bgColor = getChartBackground();
                    for (let key in chartInstances) {
                        if (chartInstances[key]) {
                            chartInstances[key].updateOptions({
                                theme: { mode: themeMode },
                                chart: { background: bgColor }
                            });
                        }
                    }
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        // CHART 1: Tren Piutang (Area)
        chartInstances['trend'] = new ApexCharts(document.querySelector("#chart-trend"), {
            series: [
                { name: '{{ __("stat_total_receivable") }}', data: {!! json_encode($trendNewAmounts) !!} },
                { name: '{{ __("stat_total_paid") }}', data: {!! json_encode($trendPaidAmounts) !!} }
            ],
            theme: { mode: getChartTheme() },
            chart: { type: 'area', height: 320, width: '100%', fontFamily: 'inherit', toolbar: { show: false }, background: getChartBackground() },
            colors: ['#818cf8', '#34d399'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            xaxis: {
                categories: {!! json_encode($trendMonths) !!},
                tooltip: { enabled: false },
                labels: { style: { fontSize: '11px' }, hideOverlappingLabels: true }
            },
            yaxis: { labels: { formatter: fmtJt, style: { fontSize: '11px' } } },
            tooltip: { y: { formatter: fmtRp } },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
            responsive: [{ breakpoint: 480, options: { chart: { height: 280 } } }]
        });
        chartInstances['trend'].render();

        // CHART 2: Realisasi Pelunasan (Bar)
        chartInstances['monthly-paid'] = new ApexCharts(document.querySelector("#chart-monthly-paid"), {
            series: [{ name: '{{ __("stat_total_paid") }}', data: {!! json_encode($paidMonthlyAmounts) !!} }],
            theme: { mode: getChartTheme() },
            chart: { type: 'bar', height: 320, width: '100%', fontFamily: 'inherit', toolbar: { show: false }, background: getChartBackground() },
            colors: ['#34d399'],
            plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: {!! json_encode($paidMonths) !!},
                labels: { style: { fontSize: '11px' }, hideOverlappingLabels: true }
            },
            yaxis: { labels: { formatter: fmtJt, style: { fontSize: '11px' } } },
            tooltip: { y: { formatter: fmtRp } },
            responsive: [{ breakpoint: 480, options: { chart: { height: 280 } } }]
        });
        chartInstances['monthly-paid'].render();

        // CHART 3: Top 5 Pelanggan (Horizontal Bar)
        chartInstances['top'] = new ApexCharts(document.querySelector("#chart-top"), {
            series: [{ name: '{{ __("stat_total_unpaid") }}', data: {!! json_encode($topTotals) !!} }],
            theme: { mode: getChartTheme() },
            chart: { type: 'bar', height: 320, width: '100%', fontFamily: 'inherit', toolbar: { show: false }, background: getChartBackground() },
            colors: ['#fbbf24'],
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: {!! json_encode($topNames) !!},
                labels: { formatter: fmtJt, hideOverlappingLabels: true, style: { fontSize: '11px' } }
            },
            yaxis: { labels: { show: true, maxWidth: 120, style: { fontSize: '11px' } } },
            tooltip: { y: { formatter: fmtRp } },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 300 },
                    yaxis: { labels: { maxWidth: 90, style: { fontSize: '10px' } } },
                    xaxis: { labels: { style: { fontSize: '9px' } } }
                }
            }]
        });
        chartInstances['top'].render();

        // CHART 4: Komposisi Status (Donut)
        chartInstances['status'] = new ApexCharts(document.querySelector("#chart-status"), {
            series: [{{ $statusPaid }}, {{ $statusUnpaid }}, {{ $statusDueSoon }}, {{ $statusOverdue }}],
            theme: { mode: getChartTheme() },
            chart: { type: 'donut', height: 320, width: '100%', fontFamily: 'inherit', background: getChartBackground() },
            labels: ['{{ __("paid") }}', '{{ __("unpaid") }}', '{{ __("due_soon") }}', '{{ __("overdue") }}'],
            colors: ['#34d399', '#60a5fa', '#fbbf24', '#fb7185'],
            plotOptions: { pie: { donut: { size: '75%' } } },
            dataLabels: { enabled: false },
            stroke: { width: 4, colors: ['transparent'] },
            legend: { position: 'bottom', markers: { radius: 12 }, itemMargin: { horizontal: 10, vertical: 5 } },
            tooltip: { y: { formatter: function(v) { return v + ' {{ __("stat_transactions") }}'; } } },
            responsive: [{
                breakpoint: 480,
                options: { chart: { height: 280 }, legend: { position: 'bottom', fontSize: '11px', itemMargin: { horizontal: 5, vertical: 2 } } }
            }]
        });
        chartInstances['status'].render();

        // CHART 5: Lunas vs Belum Lunas (Pie)
        chartInstances['pie'] = new ApexCharts(document.querySelector("#chart-pie"), {
            series: [{{ $paidAmount }}, {{ $unpaidAmount }}],
            theme: { mode: getChartTheme() },
            chart: { type: 'pie', height: 320, width: '100%', fontFamily: 'inherit', background: getChartBackground() },
            labels: ['{{ __("paid") }}', '{{ __("unpaid") }}'],
            colors: ['#34d399', '#fb7185'],
            dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 'bold' }, dropShadow: { enabled: true } },
            stroke: { width: 4, colors: ['transparent'] },
            legend: { position: 'bottom', markers: { radius: 12 }, itemMargin: { horizontal: 10, vertical: 5 } },
            tooltip: { y: { formatter: fmtRp } },
            responsive: [{
                breakpoint: 480,
                options: { chart: { height: 280 }, legend: { position: 'bottom', fontSize: '11px' }, dataLabels: { style: { fontSize: '11px' } } }
            }]
        });
        chartInstances['pie'].render();

    });
</script>
@endsection
