<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EndpointsController extends Controller
{
    public function exportPDF()
    {
        $pdf = \PDF::loadView('pdf.endpoints');
        return $pdf->download('endpoints.pdf');
    }
}
