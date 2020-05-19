<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use function Couchbase\defaultDecoder;

class AirplaneController extends Controller
{


//[initAlfa;initQ;initTheta] = posledna hodnota x

    public function index(Request $request)
    {
        $process = new Process(["octave", '-qf', '--eval', '
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
            r =0.2;
            initAlfa=0;
            initQ=0;
            initTheta=0;
            [y,t,x]=lsim(sys,r*ones(size(t)),t,[initAlfa;initQ;initTheta]);
            
            disp(x(:,3))
            
            flap = r*ones(size(t))*N-x*K\'
            ']);

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


}
