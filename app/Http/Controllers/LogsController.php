<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LogsExport;
use Maatwebsite\Excel\Facades\Excel;

class LogsController extends Controller
{
    public function exportCSV()
    {
        return Excel::download(new LogsExport, 'logs_export.csv', \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
    }

    public function exportPDF()
    {
        return Excel::download(new LogsExport, 'logs_export.pdf', \Maatwebsite\Excel\Excel::MPDF, ['Content-Type' => 'application/pdf']);
    }
}
