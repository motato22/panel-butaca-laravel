<?php

namespace App\Exports;

use App\Models\Evento;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BannerExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * Items that has event data
    */
    protected $items;
    protected $headings;

    public function __construct(array $items, array $headings)
    {
        $this->items    = $items;
        $this->headings = $headings;
    }

    /**
     * set the headings
     * @return array()
    */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * Set data from custom array
     * @return array()
    */
    public function array(): array
    {
        return $this->items;
    }

    /**
    * @return String
    */
     public function startCell(): string
    {
        return 'A1';
    }

    /**
    * @return array()
    */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => [
                'font' => [
                    'bold' => true
                ], 
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ],

            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            'A:H'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ],
        ];
    }
}
