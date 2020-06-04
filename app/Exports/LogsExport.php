<?php

namespace App\Exports;

use App\Log;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LogsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithCustomCsvSettings, WithEvents
{
    /**
     * LogsExport constructor.
     */
    public function __construct()
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Log::all();
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    public function map($log): array
    {
        return [
            $log->created_at,
            $log->service,
            $log->init_values ? $log->init_values : '-',
            $log->inputs,
            $log->status ? 'yes' : 'no',
            $log->error ? $log->error : '-'
        ];
    }

    public function headings(): array
    {
        return [
            'Date and time',
            'Used API service',
            'Initial values',
            'User input',
            'Successful execution',
            'Error message'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $headerCellsRange = 'A1:' . $event->sheet->getDelegate()->getHighestColumn() . '1';

                $allCellsRange = 'A1:' . $event->sheet->getDelegate()->getHighestColumn() . $event->sheet->getDelegate()->getHighestRow();

                $event->sheet->getParent()->getProperties()
                    ->setCreator("Dzive kody")
                    ->setTitle("Logs export")
                    ->setDescription(
                        "Logs export generated for Webte project."
                    );

                $event->sheet->styleCells(
                    $allCellsRange,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                        'font' => [
                            'size' =>  12
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );

                $event->sheet->styleCells(
                    $headerCellsRange,
                    [
                        'font' => [
                            'size' =>  14,
                            'color' => ['rgb' => 'ffffff'],
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => '004e66']
                        ]
                    ]
                );
            },
        ];
    }
}
