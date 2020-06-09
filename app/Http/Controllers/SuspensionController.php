<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Validator;
use App\Log;

class SuspensionController extends Controller
{
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'r' => 'required|numeric',
            'initX1' => 'sometimes|numeric',
            'initX1d' => 'sometimes|numeric',
            'initX2' => 'sometimes|numeric',
            'initX2d' => 'sometimes|numeric',
            'initX3' => 'sometimes|numeric',
        ]);

        $lastPosition = [0,0,0,0,0];

        if (
            isset($validatedData['initX1']) &&
            isset($validatedData['initX1d']) &&
            isset($validatedData['initX2']) &&
            isset($validatedData['initX2d']) &&
            isset($validatedData['initX3'])
        ) {
            $lastPosition = [
                $validatedData['initX1'],
                $validatedData['initX1d'],
                $validatedData['initX2'],
                $validatedData['initX2d'],
                $validatedData['initX3'],
            ];
        }

        $process = new Process(["octave", '-qf', '-W', '--eval', $this->getScript($validatedData['r'],$lastPosition)]);

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
            $this->addLog($validatedData['r'],$lastPosition,false,$error);
            return response()->json(['error' => $error], 422);
        }

        $this->addLog($validatedData['r'],$lastPosition,true);
        return response()->json(['data' => $this->parse($process->getOutput())], 200);
    }

    private function getScript($r, $lastPosition) {
        return '
            pkg load control;
            m1 = 2500; m2 = 320;
            k1 = 80000; k2 = 500000;
            b1 = 350; b2 = 15020;
            A=[0 1 0 0;-(b1*b2)/(m1*m2) 0 ((b1/m1)*((b1/m1)+(b1/m2)+(b2/m2)))-(k1/m1) -(b1/m1);b2/m2 0 -((b1/m1)+(b1/m2)+(b2/m2)) 1;k2/m2 0 -((k1/m1)+(k1/m2)+(k2/m2)) 0];
            B=[0 0;1/m1 (b1*b2)/(m1*m2);0 -(b2/m2);(1/m1)+(1/m2) -(k2/m2)];
            C=[0 0 1 0]; D=[0 0];
            Aa = [[A,[0 0 0 0]\'];[C, 0]];
            Ba = [B;[0 0]];
            Ca = [C,0]; Da = D;
            K = [0 2.3e6 5e8 0 8e6];
            sys = ss(Aa-Ba(:,1)*K,Ba,Ca,Da);
            
            t = 0:0.01:5;
            r = ' . $r . ';
            initX1=0;
            initX1d=0;
            initX2=0;
            initX2d=0;
            [y,t,x]=lsim(sys*[0;1],r*ones(size(t)),t,['. implode(";", $lastPosition) .']);            
            
            disp(x(:,1))
            
            position = x(:,3)
            
            lastPosition = x(size(x,1),:)
            ';
    }

    private function parse($response) {
        $data = explode("position =\n\n", $response);

        $data[0] = str_replace(' ', '', $data[0]);

        $secondPart = explode("lastPosition =\n\n", $data[1]);
        $secondPart[0] = str_replace(' ', '', $secondPart[0]);

        $secondPart[1] = rtrim($secondPart[1], "\n\n");
        $secondPart[1] = explode(' ', $secondPart[1]);

        $lastPosition = array_filter($secondPart[1]);
        $carPosition = explode("\n", $data[0]);
        $wheelPosition = explode("\n", $secondPart[0]);

        return [
            'carPosition' => array_filter($carPosition),
            'wheelPosition' => array_filter($wheelPosition),
            'lastPosition' => array_values($lastPosition)
        ];
    }

    private function addLog($r, $initValues, $status, $error = null){
        $data = [
            'service' => 'suspension',
            'init_values' => implode(", ", $initValues),
            'inputs' => 'r = ' . $r,
            'status' => $status,
        ];
        if ($error)
            $data['error'] = $error;

        Log::create($data);
    }

}
