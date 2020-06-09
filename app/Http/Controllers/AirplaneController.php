<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use App\Log;


class AirplaneController extends Controller
{
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'r' => 'required|numeric',
            'lr1' => 'sometimes|numeric',
            'lr2' => 'sometimes|numeric',
            'lr3' => 'sometimes|numeric'
        ]);
        $lastRow = [0,0,0];

        if (isset($validatedData['lr1'])
            && isset($validatedData['lr2'])
            && isset($validatedData['lr3'])) {
            $lastRow = [
                $validatedData['lr1'],
                $validatedData['lr2'],
                $validatedData['lr3']
            ];
        }

        $process = new Process(["octave", '-qf', '-W', '--eval', $this->getScript($validatedData['r'], $lastRow)]);

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
            $this->addLog($validatedData['r'],$lastRow,false,$error);
            return response()->json(['error' => $error], 422);
        }

        $this->addLog($validatedData['r'],$lastRow,true);
        return response()->json(['data' => $this->parse($process->getOutput())], 200);
    }

    private function parse($response) {
        $data = explode("flap =\n\n", $response);

        $data[0] = str_replace(' ', '', $data[0]);

        $secondPart = explode("lastx =\n\n", $data[1]);
        $secondPart[0] = str_replace(' ', '', $secondPart[0]);

        $secondPart[1] = rtrim($secondPart[1], "\n\n");
        $secondPart[1] = explode(' ', $secondPart[1]);

        $lastX = array_filter($secondPart[1]);
        $pitchAngle = explode("\n", $data[0]);
        $backflapAngle = explode("\n", $secondPart[0]);

        return [
            'pitchAngle' => array_filter($pitchAngle),
            'backflapAngle' => array_filter($backflapAngle),
            'lastX' => array_values($lastX)
        ];
    }

    private function getScript($r, $lastRow) {
        return '
            pkg load control;
            A = [-0.313 56.7 0; -0.0139 -0.426 0; 0 56.7 0];
            B = [0.232; 0.0203; 0];
            C = [0 0 1];
            D = [0];
            
            p = 2;
            K = lqr(A,B,p*C\'*C,1);
            N = -inv(C(1,:)*inv(A-B*K)*B);
            
            sys = ss(A-B*K, B*N, C, D);
            
            t = 0:0.1:40;
            r = ' . $r . ';
            
            [y,t,x]=lsim(sys,r*ones(size(t)),t, ['. implode(";", $lastRow) .']);
            
            disp(x(:,3))

            flap = r*ones(size(t))*N-x*K\'
            
            lastx = x(size(x,1),:)
            ';
    }

    private function addLog($r, $initValues, $status, $error = null){
        $data = [
            'service' => 'airplane',
            'init_values' => implode(", ", $initValues),
            'inputs' => 'r = ' . $r,
            'status' => $status,
        ];
        if ($error)
            $data['error'] = $error;

        Log::create($data);
    }
}
