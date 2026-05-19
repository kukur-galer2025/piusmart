<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ReceivablesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $receivables;

    // Menerima data terfilter dari Controller
    public function __construct($receivables)
    {
        $this->receivables = $receivables;
    }

    /**
     * Mengambil kumpulan data transaksi piutang.
     */
    public function collection()
    {
        return $this->receivables;
    }

    /**
     * Mengatur baris judul (Header) kolom Excel.
     */
    public function headings(): array
    {
        return [
            'Nama Pelanggan',
            'No. HP / WA',
            'Jumlah Transaksi (Rp)',
            'Tanggal Transaksi',
            'Tanggal Jatuh Tempo',
            'Status Pembayaran'
        ];
    }

    /**
     * Memetakan struktur baris data ke dalam sel Excel.
     */
    public function map($item): array
    {
        $today = Carbon::today();
        $dueDate = Carbon::parse($item->due_date)->startOfDay();
        $statusText = 'Belum Lunas';
        
        if ($item->is_paid) { $statusText = 'Lunas'; }
        elseif ($today->gt($dueDate)) { $statusText = 'Terlambat'; }
        elseif ($today->diffInDays($dueDate, false) <= 3) { $statusText = 'Akan Jatuh Tempo'; }

        return [
            $item->customer->name,
            $item->customer->phone ?? '-',
            $item->amount,
            $item->transaction_date->format('d/m/Y'),
            $item->due_date->format('d/m/Y'),
            $statusText
        ];
    }

    /**
     * Menyisipkan Baris Total Otomatis Berbasis Formula SUM Spreadsheet asli.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $totalRow = $highestRow + 1; // Baris baru tepat di bawah data terakhir

                // Menulis teks label dan rumus matematika SUM asli Excel di kolom C
                $sheet->setCellValue('B' . $totalRow, 'TOTAL AKUMULASI PIUTANG');
                $sheet->setCellValue('C' . $totalRow, '=SUM(C2:C' . $highestRow . ')');

                // Mengatur gaya visual mewah baris akumulasi total (Format Double Border Akuntansi)
                $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => '0F172A']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F1F5F9'], // Latar abu-abu elegan
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1']
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE, // Garis ganda bawah akuntansi
                            'color' => ['rgb' => '0F172A']
                        ]
                    ]
                ]);

                // Menyelaraskan teks baris akumulasi
                $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getRowDimension($totalRow)->setRowHeight(24);
            },
        ];
    }

    /**
     * Mengatur Desain, Warna, Format Angka, dan Border Excel (Luxury Theme).
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // 1. Format Mata Uang / Ribuan untuk Kolom C (Jumlah Transaksi)
        $sheet->getStyle('C2:C' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

        // 2. Desain Header Kolom (Baris 1) - Tema Muted Emerald & Bold White text
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '065F46'], // Warna Emerald Gelap Mewah
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Mengatur tinggi baris header agar terlihat longgar dan lega
        $sheet->getRowDimension(1)->setRowHeight(26);

        // 3. Desain Baris Data (Zebra Striping & Pewarnaan Teks Status)
        for ($row = 2; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->applyFromArray([
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ]);
            }

            // Pewarnaan teks status secara cerdas & kontras di Kolom F
            $statusCell = $sheet->getCell('F' . $row)->getValue();
            $statusColor = '334155';
            
            if ($statusCell === 'Lunas') { $statusColor = '047857'; }
            elseif ($statusCell === 'Terlambat') { $statusColor = 'B91C1C'; }
            elseif ($statusCell === 'Akan Jatuh Tempo') { $statusColor = 'B45309'; }

            $sheet->getStyle('F' . $row)->getFont()->applyFromArray([
                'bold' => true,
                'color' => ['rgb' => $statusColor]
            ]);
        }

        // 4. Tambahkan Garis Batas (Border) Tipis Halus ke seluruh sel tabel
        $sheet->getStyle('A1:F' . $highestRow)->getBorders()->getAllBorders()->applyFromArray([
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => 'E2E8F0'],
        ]);

        // Meratakan posisi teks di kolom tertentu agar rapi
        $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D2:E' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}