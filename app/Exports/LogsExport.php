<?php

namespace App\Exports;

use App\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LogsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithCustomCsvSettings
{
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
}
