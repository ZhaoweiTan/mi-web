<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class DashboardController extends Controller
{
    private $isLocal;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

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
    private $func_array = array(
        "Resource Block Analysis" => "rb",
        "Bandwidth Analysis" => "bw",
    );

    public function __construct()
    {
        $this->middleware('auth');
        // $this->isLocal = config('mi.local') == 'local' ? True : False;
        $this->isLocal = False; // this is just for test. Need to change back to the above code when deploying.
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function oai()
    {
        $mi_array = $this->mi_array;
        $func_array = $this->func_array;
        $oai_status = array(
            "band" => array(7, 39),
            "bandwidth" => array(5, 10, 15, 20)
        );
        return view('pages.oai', compact( "oai_status", "mi_array", "func_array"));
    }


    public function check_status()
    {
        if ($this->isLocal) {
            $keyword = "ping";
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

    public function oai_start(Request $request)
    {
        $this->generateConfig($request);
        $rtn = "";
        if ($this->isLocal) {
            $cmd = "ping -i 2 8.8.8.8 > ".public_path()."/mi/oai_log/oai_enb_log.txt";
            $rtn = exec($cmd);
        } else {
            // start OAI eNB
            $cmd = "cd ".public_path()."/mi/MI-eNB && ".
                    // "source oaienv && ". ?? not work
                    "cd cmake_targets/lte_build_oai/build && ".
                    // "ENODEB=1 sudo -E ./lte-softmodem -O ../../../ci-scripts/conf_files/enb_config.conf --basicsim --noS1 2>&1 ".
                    "ENODEB=1 sudo -E ./lte-softmodem -O ".public_path()."/../storage/app/OAI_config.conf --basicsim --noS1 ".
                    "> ".public_path()."/mi/oai_log/oai_enb_log.txt 2>&1 &";
            $rtn = exec($cmd);

            // start OAI UE
            $cmd = "cd ".public_path()."/mi/MI-eNB && ".
                    "cd cmake_targets/lte_build_oai/build && ".
                    "sudo -E ./lte-uesoftmodem -C 2350000000 -r 25 --ue-rxgain 125 --basicsim --noS1 ".
                    "> ".public_path()."/mi/oai_log/oai_ue_simulator_log.txt 2>&1 &";
            exec($cmd);
        }
        return response()->json($rtn);
    }


    public function read_file(Request $request)
    {
        if ($this->isLocal) {
            $myfile = fopen(public_path()."/mi/oai_log/oai_enb_log.txt", "r") or die("Unable to open file!" . $myfile);
        } else {
            $myfile = fopen(public_path()."/mi/oai_log/oai_enb_log.txt", "r") or die("Unable to open file!" . $myfile);
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

    public function run_analysis(Request $request) {
        if ($this->isLocal) {
            $log_path = " mi/oai_log/oai_enb_log.txt";
            $python_path = "python3";
        } else {
            $log_path = " mi/oai_log/oai_enb_log.txt";
            $python_path = "python3";
        }

        $type = $request->type[0]['value'];
        $func = "";
        foreach ($this->func_array as $k => $v) {
            if ($type == $k) {
                $func = $v;
            }
        }

        if ($func == "") {
            return response()->json();
        } else {
            $cmd = $python_path . ' mi/offline-'. $func . '.py ' . $log_path . " 2>&1";
            $res = exec($cmd);
            return response()->json($res);
        }
    }

    public function kill() {
        if ($this->isLocal) {
            $cmd = "sudo /bin/kill -9 `ps -A | grep ping | awk '{ print $1 }'` 2>&1";
        } else {
            $cmd = "sudo /bin/kill -9 `ps -A | grep softmodem | awk '{ print $1 }'` 2>&1";
        }
        $rtn = exec($cmd);
        return response()->json($rtn);
    }

    private function keyInArray($needle, $haystack) {
        foreach ($haystack as $a) {
            if ($a['name'] == $needle) {
                return $a['value'];
            }
        }
        return False;
    }


    private function generateConfig($request)
    {
//        dd($request->miconfig);
        $mi_string = "";
        foreach ($this->mi_array as $k => $v) {
            if ($this->keyInArray($k, $request->miconfig ?? array()) == 'on') {
                $mi_string = $mi_string . "$v = 1; \n";
            } else {
                $mi_string = $mi_string . "$v = 0; \n";
            }
        }
        $sys = $request->sysconfig;
//        dd($sys);
        $band = $this->keyInArray('config_band', $sys);
        $bw = $this->keyInArray('config_bandwidth', $sys) ;

        if ($band == '7') {
            $dlfreq = "2685000000L";
            $ulfreq = "-120000000";
        } elseif ($band == '39') {
            $dlfreq = "L";
            $ulfreq = "";
        }

        if ($bw == '5') {
            $nrb = 25;
        } else if ($bw == '10') {
            $nrb = 50;
        } else if ($bw == '15') {
            $nrb = 75;
        } else if ($bw == '20') {
            $nrb = 100;
        }

        $content = $this->get_oai_config_simulator($mi_string);
        Storage::disk('local')->put('OAI_config.conf', $content);
    }

    public function custom() {
        return view('pages.custom');
    }

    public function custom_analysis(Request $request) {
        $rtn_arr = array(
            "status" => 1,
        );
        $log = $request->log_file;
        $script = $request->script_file;
        $analyzer = $request->analyzer_file;
        if (!($script->isValid() && $analyzer->isValid() && $log->isValid() )) {
            $rtn_arr['status'] = 2;
            $rtn_arr['msg'] = "File upload fail. Check your file type or size.";
            return response()->json($rtn_arr);
        }

        $existed = false;
        move_uploaded_file($log, "mi/oai_log/log_custom.txt");
        move_uploaded_file($script, "mi/custom.py");
        $analyzer_name = $analyzer->getClientOriginalName();
        if (file_exists("mi/$analyzer_name")) {
            $rtn_arr['msg'] = "The analyzer of the same name already exists. Using the old analyzer...";
            $existed = true;
        } else {
            move_uploaded_file($analyzer, "mi/$analyzer_name");
        }

        if ($this->isLocal) {
            $python_path = "python3";
        } else {
            $python_path = "python3";
        }

        $cmd = $python_path . " mi/custom.py mi/oai_log/log_custom.txt 2>&1";
        $res = exec($cmd);

        if (strpos(strtolower($res), "error")) {
            $rtn_arr['status'] = 3;
        }


        $rtn_arr['result'] = $res;

        if (!$existed) {
            unlink("mi/$analyzer_name");
        }
        unlink("mi/custom.py");
        unlink("mi/oai_log/log_custom.txt");
        return response()->json($rtn_arr);
    }

    public function co_analysis (Request $request)
    {
        return;
    }

    public function get_oai_config_simulator($mi_string) {
        return "Active_eNBs = ( \"eNB_Eurecom_LTEBox\");
# Asn1_verbosity, choice in: none, info, annoying
Asn1_verbosity = \"none\";

eNBs =
(
{
    ////////// Identification parameters:
    eNB_ID    =  0xe00;

    cell_type =  \"CELL_MACRO_ENB\";

    eNB_name  =  \"eNB_Eurecom_LTEBox\";

    // Tracking area code, 0x0000 and 0xfffe are reserved values
    tracking_area_code = 1;
    plmn_list = ( { mcc = 208; mnc = 93; mnc_length = 2; } );

    ////////// Physical parameters:

    component_carriers = (
    {
        node_function                                         = \"eNodeB_3GPP\";
        node_timing                                           = \"synch_to_ext_device\";
        node_synch_ref                                        = 0;
        frame_type					      = \"TDD\";
        tdd_config 					      = 1;
        tdd_config_s            			      = 0;
        prefix_type             			      = \"NORMAL\";
        eutra_band              			      = 40;
        downlink_frequency      			      = 2350000000L;
        uplink_frequency_offset 			      = 0;
        Nid_cell					      = 0;
        N_RB_DL                 			      = 25;
        Nid_cell_mbsfn          			      = 0;
        nb_antenna_ports          			      = 1;
        nb_antennas_tx          			      = 1;
        nb_antennas_rx          			      = 1;
        tx_gain                                            = 90;
        rx_gain                                            = 125;
        prach_root              			      = 0;
        prach_config_index      			      = 0;
        prach_high_speed        			      = \"DISABLE\";
        prach_zero_correlation  			      = 1;
        prach_freq_offset       			      = 2;
        pucch_delta_shift       			      = 1;
        pucch_nRB_CQI           			      = 1;
        pucch_nCS_AN            			      = 0;
        pucch_n1_AN             			      = 0;
        pdsch_referenceSignalPower 			      =-27;
        pdsch_p_b                  			      = 0;
        pusch_n_SB                 			      = 1;
        pusch_enable64QAM          			      = \"DISABLE\";
        pusch_hoppingMode                                  = \"interSubFrame\";
        pusch_hoppingOffset                                = 0;
        pusch_groupHoppingEnabled  			      = \"ENABLE\";
        pusch_groupAssignment      			      = 0;
        pusch_sequenceHoppingEnabled		   	      = \"DISABLE\";
        pusch_nDMRS1                                       = 1;
        phich_duration                                     = \"NORMAL\";
        phich_resource                                     = \"ONESIXTH\";
        srs_enable                                         = \"DISABLE\";
    /*  srs_BandwidthConfig                                =;
        srs_SubframeConfig                                 =;
        srs_ackNackST                                      =;
        srs_MaxUpPts                                       =;*/

        pusch_p0_Nominal                                   = -96;
        pusch_alpha                                        = \"AL1\";
        pucch_p0_Nominal                                   = -106;
        msg3_delta_Preamble                                = 6;
        pucch_deltaF_Format1                               = \"deltaF2\";
        pucch_deltaF_Format1b                              = \"deltaF3\";
        pucch_deltaF_Format2                               = \"deltaF0\";
        pucch_deltaF_Format2a                              = \"deltaF0\";
        pucch_deltaF_Format2b		    	      = \"deltaF0\";

        rach_numberOfRA_Preambles                          = 64;
        rach_preamblesGroupAConfig                         = \"DISABLE\";
    /*
        rach_sizeOfRA_PreamblesGroupA                      = ;
        rach_messageSizeGroupA                             = ;
        rach_messagePowerOffsetGroupB                      = ;
    */
        rach_powerRampingStep                              = 4;
        rach_preambleInitialReceivedTargetPower            = -108;
        rach_preambleTransMax                              = 10;
        rach_raResponseWindowSize                          = 10;
        rach_macContentionResolutionTimer                  = 48;
        rach_maxHARQ_Msg3Tx                                = 4;

        pcch_default_PagingCycle                           = 128;
        pcch_nB                                            = \"oneT\";
        bcch_modificationPeriodCoeff			      = 2;
        ue_TimersAndConstants_t300			      = 1000;
        ue_TimersAndConstants_t301			      = 1000;
        ue_TimersAndConstants_t310			      = 1000;
        ue_TimersAndConstants_t311			      = 10000;
        ue_TimersAndConstants_n310			      = 20;
        ue_TimersAndConstants_n311			      = 1;

    ue_TransmissionMode				      = 1;
    }
    );


    srb1_parameters :
    {
        # timer_poll_retransmit = (ms) [5, 10, 15, 20,... 250, 300, 350, ... 500]
        timer_poll_retransmit    = 80;

        # timer_reordering = (ms) [0,5, ... 100, 110, 120, ... ,200]
        timer_reordering         = 35;

        # timer_reordering = (ms) [0,5, ... 250, 300, 350, ... ,500]
        timer_status_prohibit    = 0;

        # poll_pdu = [4, 8, 16, 32 , 64, 128, 256, infinity(>10000)]
        poll_pdu                 =  4;

        # poll_byte = (kB) [25,50,75,100,125,250,375,500,750,1000,1250,1500,2000,3000,infinity(>10000)]
        poll_byte                =  99999;

        # max_retx_threshold = [1, 2, 3, 4 , 6, 8, 16, 32]
        max_retx_threshold       =  4;
    }

    # ------- SCTP definitions
    SCTP :
    {
        # Number of streams to use in input/output
        SCTP_INSTREAMS  = 2;
        SCTP_OUTSTREAMS = 2;
    };

    ////////// MME parameters:
    mme_ip_address      = ( { ipv4       = \"CI_MME_IP_ADDR\";
                            ipv6       = \"192:168:30::17\";
                            active     = \"yes\";
                            preference = \"ipv4\";
                            }
                        );

    enable_measurement_reports = \"no\";

    ///X2
    enable_x2 = \"no\";
    t_reloc_prep      = 1000;      /* unit: millisecond */
    tx2_reloc_overall = 2000;      /* unit: millisecond */

    NETWORK_INTERFACES :
    {
        ENB_INTERFACE_NAME_FOR_S1_MME            = \"eth0\";
        ENB_IPV4_ADDRESS_FOR_S1_MME              = \"CI_ENB_IP_ADDR\";
        ENB_INTERFACE_NAME_FOR_S1U               = \"eth0\";
        ENB_IPV4_ADDRESS_FOR_S1U                 = \"CI_ENB_IP_ADDR\";
        ENB_PORT_FOR_S1U                         = 2152; # Spec 2152
        ENB_IPV4_ADDRESS_FOR_X2C                 = \"CI_ENB_IP_ADDR\";
        ENB_PORT_FOR_X2C                         = 36422; # Spec 36422
    };

    log_config :
    {
    global_log_level                      =\"debug\";
    global_log_verbosity                  =\"medium\";
    hw_log_level                          =\"info\";
    hw_log_verbosity                      =\"medium\";
    phy_log_level                         =\"info\";
    phy_log_verbosity                     =\"medium\";
    mac_log_level                         =\"info\";
    mac_log_verbosity                     =\"high\";
    rlc_log_level                         =\"info\";
    rlc_log_verbosity                     =\"medium\";
    pdcp_log_level                        =\"info\";
    pdcp_log_verbosity                    =\"medium\";
    rrc_log_level                         =\"info\";
    rrc_log_verbosity                     =\"medium\";
};

}
);
MACRLCs = (
    {
        num_cc = 1;
        tr_s_preference = \"local_L1\";
        tr_n_preference = \"local_RRC\";
        scheduler_mode = \"fairRR\";
        puSch10xSnr     =  200;
        puCch10xSnr     =  200;
        }  
);

L1s = (
        {
    num_cc = 1;
    tr_n_preference = \"local_mac\";
        }  
);

RUs = (
    {		  
    local_rf       = \"yes\"
        nb_tx          = 1
        nb_rx          = 1
        att_tx         = 0
        att_rx         = 0;
        bands          = [38];
        max_pdschReferenceSignalPower = -27;
        max_rxgain                    = 125;
        eNB_instances  = [0];

    }
);  

THREAD_STRUCT = (
{
    #three config for level of parallelism \"PARALLEL_SINGLE_THREAD\", \"PARALLEL_RU_L1_SPLIT\", or \"PARALLEL_RU_L1_TRX_SPLIT\"
    parallel_config    = \"PARALLEL_SINGLE_THREAD\";
    #two option for worker \"WORKER_DISABLE\" or \"WORKER_ENABLE\"
    worker_config      = \"WORKER_ENABLE\";
}
);

NETWORK_CONTROLLER :
{
    FLEXRAN_ENABLED        = \"no\";
    FLEXRAN_INTERFACE_NAME = \"ens3\";
    FLEXRAN_IPV4_ADDRESS   = \"CI_FLEXRAN_CTL_IP_ADDR\";
    FLEXRAN_PORT           = 2210;
    FLEXRAN_CACHE          = \"/mnt/oai_agent_cache\";
    FLEXRAN_AWAIT_RECONF   = \"no\";
};

mi_log_level:
{
    $mi_string
};";
    }
}
