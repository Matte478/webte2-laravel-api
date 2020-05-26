<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LogsExport;
use Maatwebsite\Excel\Facades\Excel;

class LogsController extends Controller
{
    public function export()
    {
        return Excel::download(new LogsExport, 'logs_export.csv', \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
    }
}
