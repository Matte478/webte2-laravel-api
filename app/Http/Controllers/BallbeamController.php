<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use App\Log;


class BallbeamController extends Controller
{
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'r' => 'required|numeric',
            'lr1' => 'sometimes|numeric',
            'lr2' => 'sometimes|numeric',
            'lr3' => 'sometimes|numeric',
            'lr4' => 'sometimes|numeric',
        ]);

        $lastRow = [0,0,0,0];

        if (
            isset($validatedData['lr1']) &&
            isset($validatedData['lr2']) &&
            isset($validatedData['lr3']) &&
            isset($validatedData['lr4'])
        ) {
            $lastRow = [
                $validatedData['lr1'],
                $validatedData['lr2'],
                $validatedData['lr3'],
                $validatedData['lr4'],
            ];
        }

        $process = new Process(["octave", '-qf', '-W', '--eval', $this->getScript($validatedData['r'], $lastRow)]);

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
            $this->addLog($validatedData['r'],$lastRow,false,$error);
            return response()->json(['error' => $process->getErrorOutput()], 422);
        }

        $this->addLog($validatedData['r'],$lastRow,true);
        return response()->json(['data' => $this->parse($process->getOutput())], 200);
    }

    private function parse($response) {
        $data = explode("angle =\n\n", $response);

        $data[0] = str_replace(' ', '', $data[0]);

        $secondPart = explode("lastX =\n\n", $data[1]);
        $secondPart[0] = str_replace(' ', '', $secondPart[0]);

        $secondPart[1] = rtrim($secondPart[1], "\n\n");
        $secondPart[1] = explode(' ', $secondPart[1]);

        $lastX = array_filter($secondPart[1]);
        $position = explode("\n", $data[0]);
        $angle = explode("\n", $secondPart[0]);

        return [
            'position' => array_filter($position),
            'angle' => array_filter($angle),
            'lastX' => array_values($lastX)
        ];
    }

    private function getScript($r, $lastRow) {
        return '
            pkg load control;
            m = 0.111;
            R = 0.015;
            g = -9.8;
            J = 9.99e-6;
            H = -m*g/(J/(R^2)+m);
            A = [0 1 0 0; 0 0 H 0; 0 0 0 1; 0 0 0 0];
            B = [0;0;0;1];
            C = [1 0 0 0];
            D = [0];   
            K = place(A,B,[-2+2i,-2-2i,-20,-80]);
            N = -inv(C*inv(A-B*K)*B);
            
            sys = ss(A-B*K,B,C,D);
            
            t = 0:0.01:5;
            r = ' . $r . ';
            [y,t,x]=lsim(N*sys,r*ones(size(t)),t,['. implode(";", $lastRow) .']);
            
            disp(N*x(:,1))
            
            angle = x(:,3)
            
            lastX = x(size(x,1),:)
            ';
    }

    private function addLog($r, $initValues, $status, $error = null){
        $data = [
            'service' => 'ballBeam',
            'init_values' => implode(", ", $initValues),
            'inputs' => 'r = ' . $r,
            'status' => $status,
        ];
        if ($error)
            $data['error'] = $error;

        Log::create($data);
    }
}
