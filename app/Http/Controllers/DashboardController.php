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

    private $isLocal = True;
    private $mi_array = array(
        "0xB0A3" => "LTE_PDCP_DL_Cipher_Data_PDU",
        "0xB0B3" => "LTE_PDCP_UL_Cipher_Data_PDU",
        "0xB173" => "LTE_PHY_PDSCH_Stat_Indication",
        "0xB063" => "LTE_MAC_DL_Transport_Block",
        "0xB064" => "LTE_MAC_UL_Transport_Block",
        "0xB092" => "LTE_RLC_UL_AM_All_PDU",
        "0xB082" => "LTE_RLC_DL_AM_All_PDU",
        "0xB13C" => "LTE_PHY_PUCCH_SR",
    );

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
        $mi_array = $this->mi_array;
        $oai_status = array(
            "status" => "On",
            "time" => "20 hrs"
        );
        return view('pages.oai', compact("system_config", "mi_config", "oai_status", "mi_array"));
    }




    public function check_status()
    {
        if ($this->isLocal) {
            $keyword = "8.8.8.8";
        } else {
            $keyword = "softmodem";
        }
        $cmd = "ps -A | grep -v grep | grep ". $keyword;
        $rtn = exec($cmd);
        if ($rtn == "") {
            return response()->json("Off");
        } else {
            return response()->json("On");
        }
    }

    private function generateConfig()
    {

    }

    public function mi()
    {
        $system_config = "Configured";
        $mi_config = "Configured";
        $mi_array = $this->mi_array;
        $oai_status = array(
            "status" => "On",
            "time" => "0 hr"
        );
        return view('pages.mi', compact("system_config", "mi_config", "oai_status", "mi_array"));
    }

    public function oai_start(Request $request)
    {
        if ($this->isLocal) {
            $cmd = "ping -i 2 8.8.8.8 > /Users/tan/Documents/Development/mobiq/mi-web/public/log/log.txt";
        } else {
            $cmd = "sudo /home/wing/nfv/openairinterface5g/cmake_targets/lte_build_oai/build/lte-softmodem -O /home/wing/nfv/openairinterface5g/targets/PROJECTS/GENERIC-LTE-EPC/CONF/test.conf -d 2>&1  | tee /var/www/html/mi/public/log/log.txt";
        }
        $rtn = exec($cmd);
        return response()->json($rtn);
    }


    public function read_file(Request $request)
    {
        if ($this->isLocal) {
            $myfile = fopen("/Users/tan/Documents/Development/mobiq/mi-web/public/log/log.txt", "r") or die("Unable to open file!" . $myfile);
        } else {
            $myfile = fopen("/var/www/html/mi/public/log/log.txt", "r") or die("Unable to open file!" . $myfile);
        }
        $res = "";

        $keyword = $request->post('keyword') ?? "";

        while ($line = fgets($myfile)) {
            if ($keyword == "") {
                $res = $res . $line . '</br>';
            } else {
                if (strpos($line, $keyword)) {
                    $res = $res . $line . '</br>';
                }
            }
        }
        fclose($myfile);
        return response()->json($res);

    }

    public function run_analysis($type) {
        return response()->json();
    }

    public function kill() {
        if ($this->isLocal) {
            $cmd = "kill -9 `ps -A | grep ping | awk '{ print $1 }'` 2>&1";
        } else {
            $cmd = "kill -9 `ps -A | grep softmodem | awk '{ print $1 }'` 2>&1";
        }
        $rtn = exec($cmd);
        return response()->json($rtn);
    }
}
