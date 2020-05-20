<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use function Couchbase\defaultDecoder;

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

        $process = new Process(["octave", '-qf', '--eval', $this->getScript($validatedData['r'], $lastRow)]);

        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['error' => $process->getErrorOutput()], 200);
        }
        
        return response()->json(['data' => $this->parse($process->getOutput())], 200);
    }

    private function parse($response) {
        $data = explode("flap =\n\n", $response);

        $data[0] = str_replace(' ', '', $data[0]);
        $data[1] = str_replace(' ', '', $data[1]);

        $pitchAngle = explode("\n", $data[0]);
        $backflapAngle = explode("\n", $data[1]);

        return [
            'pitchAngle' => array_filter($pitchAngle),
            'backflapAngle' => array_filter($backflapAngle)
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
            ';
    }


}
