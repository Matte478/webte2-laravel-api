<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use App\Log;


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
            $error = $process->getErrorOutput();
            $this->addLog($validatedData['problem'],false,$error);
            return response()->json(['error' => $process->getErrorOutput()], 200);
        }

        $this->addLog($validatedData['problem'],true);
        return response()->json(['data' => $process->getOutput()], 200);
    }

    private function addLog($problem, $status, $error = null, $initValues = null){
        $data = [
            'service' => 'custom',
            'inputs' => $problem,
            'status' => $status,
        ];
        if ($error)
            $data['error'] = $error;
        if ($initValues)
            $data['init_values'] = implode(", ", $initValues);

        Log::create($data);
    }
}
