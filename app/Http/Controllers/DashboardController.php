<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function oai()
    {
        $system_config = "Configured";
        $mi_config = "Configured";
        $oai_status = array(
            "status" => "On",
            "time" => "20 hrs"
        );
        return view('pages.oai', compact("system_config", "mi_config", "oai_status"));
    }

    public function mi()
    {
        $system_config = "Configured";
        $mi_config = "Configured";
        $oai_status = array(
            "status" => "On",
            "time" => "20 hrs"
        );
        return view('pages.mi', compact("system_config", "mi_config", "oai_status"));
    }

    public function oai_start(Request $request)
    {
        $isLocal = False;
        if ($isLocal) {
            $cmd = "ping 8.8.8.8 > /Users/tan/Documents/Development/mobiq/mi-web/public/log/log.txt";
            $myfile = fopen("/Users/tan/Documents/Development/mobiq/mi-web/public/log/test.txt", "r") or die("Unable to open file!" . $myfile);
        } else {
            $cmd = "sudo /home/wing/nfv/openairinterface5g/cmake_targets/lte_build_oai/build/lte-softmodem -O /home/wing/nfv/openairinterface5g/targets/PROJECTS/GENERIC-LTE-EPC/CONF/test.conf -d 2>&1  | tee /var/www/html/mi/public/log/log.txt";
            $myfile = fopen("/var/www/html/mi/public/log/log.txt", "r") or die("Unable to open file!" . $myfile);
        }
        
        if (!empty($request->post('runSign1'))) {
            $rtn = exec($cmd);
            return response()->json($rtn);
        }
        if (!empty($request->post('readSign1'))) {
            $res = "";
            while ($line = fgets($myfile)) {
                $res = $res . $line . '</br>';
            }
            fclose($myfile);
            return response()->json($res);
        }
        if (!empty($request->post('runAna1'))) {
            return response()->json();
        }
    }
}
