<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {
        return $this->customers;
    }

    public function headings(): array
    {
        return [
            __('excel_customer_id'),
            __('excel_customer_full_name'),
            __('excel_customer_phone'),
            __('excel_customer_address'),
            __('excel_customer_registered'),
        ];
    }

    public function map($item): array
    {
        return [
            'CUST-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
            $item->name,
            $item->phone ?? '-',
            $item->address ?? '-',
            $item->created_at->format('d/m/Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Desain Header
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        // Zebra Striping & Border
        for ($row = 2; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->applyFromArray([
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ]);
            }
        }

        $sheet->getStyle('A1:E' . $highestRow)->getBorders()->getAllBorders()->applyFromArray([
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => 'E2E8F0'],
        ]);

        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}