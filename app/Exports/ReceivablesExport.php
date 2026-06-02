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
    protected $statusPaid;
    protected $statusOverdue;
    protected $statusDueSoon;
    protected $statusUnpaid;

    // Menerima data terfilter dari Controller
    public function __construct($receivables)
    {
        $this->receivables = $receivables;

        // Cache translated status labels at construction time
        $this->statusPaid = __('excel_status_paid');
        $this->statusOverdue = __('excel_status_overdue');
        $this->statusDueSoon = __('excel_status_due_soon');
        $this->statusUnpaid = __('excel_status_unpaid');
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
            __('excel_receivable_customer_name'),
            __('excel_receivable_phone'),
            __('excel_receivable_item_name'),
            __('excel_receivable_amount'),
            __('excel_receivable_transaction_date'),
            __('excel_receivable_due_date'),
            __('excel_receivable_status'),
        ];
    }

    /**
     * Memetakan struktur baris data ke dalam sel Excel.
     */
    public function map($item): array
    {
        $today = Carbon::today();
        $dueDate = Carbon::parse($item->due_date)->startOfDay();
        $statusText = $this->statusUnpaid;
        
        if ($item->is_paid) { $statusText = $this->statusPaid; }
        elseif ($today->gt($dueDate)) { $statusText = $this->statusOverdue; }
        elseif ($today->diffInDays($dueDate, false) <= 3) { $statusText = $this->statusDueSoon; }

        return [
            $item->customer->name,
            $item->customer->phone ?? '-',
            $item->item_name,
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

                // Menulis teks label dan rumus matematika SUM asli Excel di kolom D
                $sheet->setCellValue('C' . $totalRow, __('excel_total_label'));
                $sheet->setCellValue('D' . $totalRow, '=SUM(D2:D' . $highestRow . ')');

                // Mengatur gaya visual mewah baris akumulasi total (Format Double Border Akuntansi)
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray([
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
                $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
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

        // 1. Format Mata Uang / Ribuan untuk Kolom D (Jumlah Transaksi)
        $sheet->getStyle('D2:D' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

        // 2. Desain Header Kolom (Baris 1) - Tema Muted Emerald & Bold White text
        $sheet->getStyle('A1:G1')->applyFromArray([
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
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->applyFromArray([
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ]);
            }

            // Pewarnaan teks status secara cerdas & kontras di Kolom G
            $statusCell = $sheet->getCell('G' . $row)->getValue();
            $statusColor = '334155';
            
            if ($statusCell === $this->statusPaid) { $statusColor = '047857'; }
            elseif ($statusCell === $this->statusOverdue) { $statusColor = 'B91C1C'; }
            elseif ($statusCell === $this->statusDueSoon) { $statusColor = 'B45309'; }

            $sheet->getStyle('G' . $row)->getFont()->applyFromArray([
                'bold' => true,
                'color' => ['rgb' => $statusColor]
            ]);
        }

        // 4. Tambahkan Garis Batas (Border) Tipis Halus ke seluruh sel tabel
        $sheet->getStyle('A1:G' . $highestRow)->getBorders()->getAllBorders()->applyFromArray([
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => 'E2E8F0'],
        ]);

        // Meratakan posisi teks di kolom tertentu agar rapi
        $sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E2:F' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}