<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ __('pdf_report_title') }}</title>
    <style>
        /* Pengaturan Kertas dan Font Dasar */
        @page { 
            margin: 50px 50px 60px 50px; 
            @bottom-right {
                content: "{{ __('pdf_page') }} " counter(page) " {{ __('pdf_of') }} " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8px;
                color: #94a3b8;
            }
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
        }

        /* KOP SURAT / HEADER */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #10b981; /* Aksen Hijau Emerald */
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table td { border: none; padding: 0; }
        .logo-container { width: 35%; vertical-align: middle; }
        .logo { max-height: 40px; width: auto; display: block; }
        .company-info { width: 65%; text-align: right; vertical-align: middle; }
        .company-name { font-size: 16px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .company-address { font-size: 9px; color: #64748b; margin-top: 4px; line-height: 1.3; }

        /* JUDUL DOKUMEN */
        .report-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #0f172a;
            letter-spacing: 1px;
        }

        /* META INFO */
        .meta-table { width: 100%; margin-bottom: 15px; }
        .meta-table td { border: none; padding: 2px 0; font-size: 10px; color: #475569; vertical-align: top; }

        /* TABEL DATA UTAMA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            border: 1px solid #cbd5e1;
            padding: 9px 8px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 8px;
            font-size: 10px;
            vertical-align: middle;
        }
        
        .data-table tr { page-break-inside: avoid; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; color: #0f172a; }
        .whitespace-nowrap { white-space: nowrap; }
        
        /* Pewarnaan Status (Badges) */
        .status {
            display: inline-block;
            padding: 4px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1;
            text-align: center;
        }
        .status-lunas { background-color: #e6fcf5; color: #0c8558; border: 1px solid #c3fae8; }
        .status-terlambat { background-color: #fff5f5; color: #c92a2a; border: 1px solid #ffe3e3; }
        .status-h3 { background-color: #fff9db; color: #e67700; border: 1px solid #fff3bf; }
        .status-belum { background-color: #edf2ff; color: #364fc7; border: 1px solid #dbe4ff; }

        /* AREA TANDA TANGAN */
        .signature-container {
            width: 100%;
            page-break-inside: avoid;
            margin-top: 30px;
        }
        .signature-table {
            width: 100%;
            float: right;
            max-width: 250px;
        }
        .signature-table td {
            border: none;
            text-align: center;
            font-size: 11px;
            padding: 0;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo Piusmart" class="logo">
            </td>
            <td class="company-info">
                <div class="company-name">PIUSMART EXECUTIVE</div>
                <div class="company-address">
                    {{ __('pdf_company_desc') }}<br>
                    {{ __('pdf_company_doc') }}
                </div>
            </td>
        </tr>
    </table>

    <div class="report-title">{{ __('pdf_receivable_title') }}</div>

    <table class="meta-table">
        <tr>
            <td width="50%">
                <strong>{{ __('pdf_print_time') }}</strong> {{ $dateReport }} WIB<br>
                <strong>{{ __('pdf_total_transactions') }}</strong> {{ $receivables->count() }} {{ __('pdf_record_data') }}
            </td>
            <td width="50%" class="text-right">
                <strong>{{ __('pdf_status_criteria') }}</strong> <span style="color: #0284c7; font-weight: bold;">{{ $filterStatus }}</span><br>
                <strong>{{ __('pdf_transaction_period') }}</strong> <span style="color: #0284c7; font-weight: bold;">{{ $filterPeriode }}</span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">{{ __('pdf_no') }}</th>
                <th style="width: 33%;">{{ __('pdf_customer_name') }}</th>
                <th style="width: 20%;" class="text-right">{{ __('pdf_amount_rp') }}</th>
                <th style="width: 14%; text-align: center;">{{ __('pdf_transaction_date') }}</th>
                <th style="width: 14%; text-align: center;">{{ __('pdf_due_date') }}</th>
                <th style="width: 14%; text-align: center;">{{ __('pdf_status') }}</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalAll = 0; 
                $no = 1; 
            @endphp
            
            @forelse($receivables as $item)
                @php
                    $totalAll += $item->amount;
                    $today = \Carbon\Carbon::today();
                    $dueDate = \Carbon\Carbon::parse($item->due_date)->startOfDay();
                    
                    if ($item->is_paid) {
                        $styleClass = 'status-lunas'; $label = __('pdf_status_paid');
                    } elseif ($today->gt($dueDate)) {
                        $styleClass = 'status-terlambat'; $label = __('pdf_status_overdue');
                    } elseif ($today->diffInDays($dueDate, false) <= 3) {
                        $styleClass = 'status-h3'; $label = __('pdf_status_due_soon'); // 🟢 Sudah diganti biar dinamis
                    } else {
                        $styleClass = 'status-belum'; $label = __('pdf_status_unpaid');
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <div class="font-bold">{{ $item->customer->name }}</div>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ __('pdf_phone') }} {{ $item->customer->phone ?? '-' }}</div>
                    </td>
                    <td class="text-right font-bold whitespace-nowrap">{{ number_format($item->amount, 0, ',', '.') }}</td>
                    <td class="text-center whitespace-nowrap">{{ $item->transaction_date->format('d/m/Y') }}</td>
                    <td class="text-center whitespace-nowrap">{{ $item->due_date->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <span class="status {{ $styleClass }}">{{ $label }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #94a3b8; font-style: italic;">
                        {{ __('pdf_no_data') }}
                    </td>
                </tr>
            @endforelse
            
            @if($receivables->isNotEmpty())
                <tr style="background-color: #f8fafc;">
                    <td colspan="2" class="font-bold text-center" style="font-size: 9px; padding: 10px; letter-spacing: 0.5px;">{{ __('pdf_total_accumulation') }}</td>
                    <td class="text-right font-bold" style="font-size: 11px; padding: 10px; color: #10b981;">{{ number_format($totalAll, 0, ',', '.') }}</td>
                    <td colspan="3" style="background-color: #f8fafc;"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td>
                    <p style="margin-bottom: 50px;">{{ __('pdf_signature_city') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>{{ __('pdf_signature_role') }}</p>
                    <p class="font-bold uppercase" style="text-decoration: underline; margin-bottom: 2px;">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p style="font-size: 8px; color: #64748b; text-transform: uppercase;">Piusmart Control Authority</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>