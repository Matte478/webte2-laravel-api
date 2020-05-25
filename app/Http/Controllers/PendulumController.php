<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Validator;


class PendulumController extends Controller
{
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'r' => 'required|numeric',
            'startDegree' => 'sometimes|numeric',
            'startPosition' => 'sometimes|numeric',
        ]);
//        $lastRow = [0,0];
           if (!isset($validatedData['startDegree']))
               $validatedData['startDegree'] = 0;
           if (!isset($validatedData['startPosition']))
               $validatedData['startPosition'] = 0;
//        if (isset($validatedData['startDegree']) && isset($validatedData['lr2']) {
//            $lastRow = [
//                $validatedData['lr1'],
//                $validatedData['lr2'],
//                $validatedData['lr3']
//            ];
//        }

        $process = new Process(["octave", '-qf', '-W', '--eval', $this->getScript($validatedData['r'],$validatedData['startDegree'],$validatedData['startPosition'])]);

        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['error' => $process->getErrorOutput()], 200);
        }

        return response()->json(['data' => $this->parse($process->getOutput())], 200);
    }

    private function getScript($r, $statDegree, $startPosition) {
        return '
            pkg load control;
            M = .5;
            m = 0.2;
            b = 0.1;
            I = 0.006;
            g = 9.8;
            l = 0.3;
            p = I*(M+m)+M*m*l^2;
            A = [0 1 0 0; 0 -(I+m*l^2)*b/p (m^2*g*l^2)/p 0; 0 0 0 1; 0 -(m*l*b)/p m*g*l*(M+m)/p 0];
            B = [ 0; (I+m*l^2)/p; 0; m*l/p];
            C = [1 0 0 0; 0 0 1 0];
            D = [0; 0];
            K = lqr(A,B,C\' * C,1);
            Ac = [(A - B * K)];
            N = -inv(C(1,:)*inv(A - B * K) * B);
            
            sys = ss(Ac, B * N, C, D);
            
            t = 0:0.05:10;
            r = ' . $r . ';
            initPozicia = ' . $startPosition . ';
            initUhol = ' . $statDegree . ';
            [y, t, x] = lsim(sys, r * ones(size(t)), t, [initPozicia;0;initUhol;0]);
            
            disp(x(:,1))
            
            angle = x(:,3)
            ';
    }

    private function parse($response) {
        $data = explode("angle =\n\n", $response);

        $data[0] = str_replace(' ', '', $data[0]);
        $data[1] = str_replace(' ', '', $data[1]);

        $position = explode("\n", $data[0]);
        $anglePendulum = explode("\n", $data[1]);

        return [
            'position' => array_filter($position),
            'anglePendulum' => array_filter($anglePendulum),
        ];
    }
}
