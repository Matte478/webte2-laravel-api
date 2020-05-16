<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CasController extends Controller
{
    public function calculate(Request $request)
    {
        $validatedData = $request->validate([
            'problem' => 'required|string'
        ]);
        
        $process = new Process(["octave", '-qf', '--eval', 'printf("%f",' . $validatedData['problem'] . ');']);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['error' => $process->getErrorOutput()], 200);
        }
        return response()->json(['data' => $process->getOutput()], 200);
    }
}
