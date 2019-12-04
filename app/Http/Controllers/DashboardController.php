<?php

namespace App\Http\Controllers;

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

    public function oai_start(Request $request)
    {
        if (!empty($request->post('runSign1'))) {
            $cmd = "sudo /home/wing/nfv/openairinterface5g/cmake_targets/lte_build_oai/build/lte-softmodem -O /home/wing//nfv/openairinterface5g/targets/PROJECTS/GENERIC-LTE-EPC/CONF/test.conf -d 2>&1  | tee /var/www/html/mi/public/log/log.txt";
            shell_exec($cmd);
        }
        if (!empty($request->post('readSign1'))) {
            $myfile = fopen("/var/www/html/mi/public/log/log.txt", "r") or die("Unable to open file!" . $myfile);
            $res = "";
            while ($line = fgets($myfile)) {
                $res = $res . $line . '</br>';
            }
            fclose($myfile);
            return json($res);
        }
    }
}
