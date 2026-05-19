<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pelanggan - Piusmart Executive</title>
    <style>
        @page { margin: 40px 50px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #334155; line-height: 1.4; }
        .header-table { width: 100%; border-bottom: 2px solid #10b981; padding-bottom: 12px; margin-bottom: 20px; }
        .header-table td { border: none; padding: 0; }
        .logo-container { width: 35%; vertical-align: middle; }
        .logo { max-height: 45px; width: auto; }
        .company-info { width: 65%; text-align: right; vertical-align: middle; }
        .company-name { font-size: 18px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .company-address { font-size: 10px; color: #64748b; margin-top: 4px; }
        .report-title { text-align: center; font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; color: #0f172a; letter-spacing: 1px; text-decoration: underline; }
        .meta-table { width: 100%; margin-bottom: 12px; }
        .meta-table td { border: none; padding: 2px 0; font-size: 10px; color: #475569; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { background-color: #f8fafc; color: #0f172a; font-weight: bold; text-transform: uppercase; font-size: 9px; border: 1px solid #cbd5e1; padding: 8px 6px; text-align: left; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; font-size: 10px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; color: #0f172a; }
        .signature-area { width: 100%; margin-top: 40px; }
        .signature-area td { border: none; text-align: right; padding-right: 30px; font-size: 11px; }
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
                <div class="company-address">Sistem Manajemen Pencatatan Piutang Usaha Terpadu<br>Dokumen dicetak secara otomatis oleh sistem</div>
            </td>
        </tr>
    </table>

    <div class="report-title">MASTER DATA PELANGGAN / DEBITUR</div>

    <table class="meta-table">
        <tr>
            <td width="60%"><strong>Waktu Cetak:</strong> {{ $dateReport }} WIB<br><strong>Total Pelanggan:</strong> {{ $customers->count() }} Orang</td>
            <td width="40%" style="text-align: right;"><strong>Klasifikasi Dokumen:</strong> Internal Administrasi<br><strong>Dicetak Oleh:</strong> {{ Auth::user()->name ?? 'Administrator' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 20%;">Nomor HP/WA</th>
                <th style="width: 35%;">Alamat</th>
                <th style="width: 15%; text-align: center;">Tgl Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($customers as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="font-bold">{{ $item->name }}</td>
                    <td>{{ $item->phone ?? '-' }}</td>
                    <td>{{ $item->address ?? '-' }}</td>
                    <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center" style="padding: 25px; color: #94a3b8;">Tidak ada data pelanggan ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-area">
        <tr>
            <td>
                <p>Mengetahui,</p><br><br><br><br>
                <p class="font-bold uppercase" style="text-decoration: underline;">{{ Auth::user()->name ?? 'Administrator' }}</p>
                <p style="font-size: 9px; color: #64748b;">Piusmart Executive Control</p>
            </td>
        </tr>
    </table>
</body>
</html>