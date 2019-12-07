<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $this->isLocal = config('mi.local') == 'local' ? True : False;
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
            "band" => array(7),
            "bandwidth" => array(5, 10, 15)
        );
        return view('pages.oai', compact( "oai_status", "mi_array", "func_array"));
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

    public function oai_start(Request $request)
    {
        $this->generateConfig($request);
        if ($this->isLocal) {
            $cmd = "ping -i 2 8.8.8.8 > /Users/tan/Documents/Development/mobiq/mi-web/public/log/log.txt";
        } else {
            $cmd = "sudo /home/wing/nfv/openairinterface5g/cmake_targets/lte_build_oai/build/lte-softmodem -O /var/www/html/mi/storage/app/tmp.conf -d 2>&1  | tee /var/www/html/mi/public/log/log.txt";
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

    public function run_analysis(Request $request) {
        if ($this->isLocal) {
            $log_path = " mi/mac3.txt";
            $python_path = "/usr/local/bin/python3";
        } else {
            $log_path = " mi/mac3.txt";
            $python_path = "/usr/bin/python3";
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
            $cmd = "kill -9 `ps -A | grep ping | awk '{ print $1 }'` 2>&1";
        } else {
            $cmd = "sudo kill -9 `ps -A | grep softmodem | awk '{ print $1 }'` 2>&1";
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
            if ($bw == '5') {
                $nrb = 25;
            } else if ($bw == '10') {
                $nrb = 50;
            } else if ($bw == '15') {
                $nrb = 75;
            } else if ($bw == '20') {
                $nrb = 100;
            }
        }

        $content = "
Active_eNBs = ( \"eNB-Eurecom-LTEBox\");
# Asn1_verbosity, choice in: none, info, annoying
Asn1_verbosity = \"none\";

eNBs =
(
 {
    ////////// Identification parameters:
    eNB_ID    =  0xe00;

    cell_type =  \"CELL_MACRO_ENB\";

    eNB_name  =  \"eNB-Eurecom-LTEBox\";

    // Tracking area code, 0x0000 and 0xfffe are reserved values
    tracking_area_code  =  1;

    plmn_list = ( { mcc = 901; mnc = 70; mnc_length = 2; } );

    tr_s_preference     = \"local_mac\"

    ////////// Physical parameters:

    component_carriers = (
      {
      node_function             = \"3GPP_eNODEB\";
      node_timing               = \"synch_to_ext_device\";
      node_synch_ref            = 0;
      frame_type					      = \"FDD\";
      tdd_config 					      = 3;
      tdd_config_s            			      = 0;
      prefix_type             			      = \"NORMAL\";
      eutra_band              			      = $band;
      downlink_frequency      			      = $dlfreq;
      uplink_frequency_offset 			      = $ulfreq;
      Nid_cell					      = 0;
      N_RB_DL                 			      = $nrb;
      Nid_cell_mbsfn          			      = 0;
      nb_antenna_ports                                = 1;
      nb_antennas_tx          			      = 1;
      nb_antennas_rx          			      = 1;
      tx_gain                                            = 90;
      rx_gain                                            = 125;
      pbch_repetition                                 = \"FALSE\";
      prach_root              			      = 0;
      prach_config_index      			      = 0;
      prach_high_speed        			      = \"DISABLE\";
      prach_zero_correlation  			      = 1;
      prach_freq_offset       			      = 2;
      pucch_delta_shift       			      = 1;
      pucch_nRB_CQI           			      = 0;
      pucch_nCS_AN            			      = 0;
      pucch_n1_AN             			      = 0;
      pdsch_referenceSignalPower 			      = -27;
      pdsch_p_b                  			      = 0;
      pusch_n_SB                 			      = 1;
      pusch_enable64QAM          			      = \"ENABLE\";
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
      pucch_p0_Nominal                                   = -104;
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
      ue_TransmissionMode                                    = 1;

      //Parameters for SIB18
      rxPool_sc_CP_Len                                       = \"normal\";
      rxPool_sc_Period                                       = \"sf40\";
      rxPool_data_CP_Len                                     = \"normal\";
      rxPool_ResourceConfig_prb_Num                          = 20;
      rxPool_ResourceConfig_prb_Start                        = 5;
      rxPool_ResourceConfig_prb_End                          = 44;
      rxPool_ResourceConfig_offsetIndicator_present          = \"prSmall\";
      rxPool_ResourceConfig_offsetIndicator_choice           = 0;
      rxPool_ResourceConfig_subframeBitmap_present           = \"prBs40\";
      rxPool_ResourceConfig_subframeBitmap_choice_bs_buf              = \"00000000000000000000\";
      rxPool_ResourceConfig_subframeBitmap_choice_bs_size             = 5;
      rxPool_ResourceConfig_subframeBitmap_choice_bs_bits_unused      = 0;
/*    rxPool_dataHoppingConfig_hoppingParameter                       = 0;
      rxPool_dataHoppingConfig_numSubbands                            = \"ns1\";
      rxPool_dataHoppingConfig_rbOffset                               = 0;
      rxPool_commTxResourceUC-ReqAllowed                              = \"TRUE\";
*/
      // Parameters for SIB19
      discRxPool_cp_Len                                               = \"normal\"
      discRxPool_discPeriod                                           = \"rf32\"
      discRxPool_numRetx                                              = 1;
      discRxPool_numRepetition                                        = 2;
      discRxPool_ResourceConfig_prb_Num                               = 5;
      discRxPool_ResourceConfig_prb_Start                             = 3;
      discRxPool_ResourceConfig_prb_End                               = 21;
      discRxPool_ResourceConfig_offsetIndicator_present               = \"prSmall\";
      discRxPool_ResourceConfig_offsetIndicator_choice                = 0;
      discRxPool_ResourceConfig_subframeBitmap_present                = \"prBs40\";
      discRxPool_ResourceConfig_subframeBitmap_choice_bs_buf          = \"f0ffffffff\";
      discRxPool_ResourceConfig_subframeBitmap_choice_bs_size         = 5;
      discRxPool_ResourceConfig_subframeBitmap_choice_bs_bits_unused  = 0;

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
    mme_ip_address      = ( { ipv4       = \"192.168.60.142\";
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
        ENB_INTERFACE_NAME_FOR_S1_MME            = \"vboxnet0\";
        ENB_IPV4_ADDRESS_FOR_S1_MME              = \"192.168.60.1\";
        ENB_INTERFACE_NAME_FOR_S1U               = \"vboxnet0\";
        ENB_IPV4_ADDRESS_FOR_S1U                 = \"192.168.60.1\";
        ENB_PORT_FOR_S1U                         = 2152; # Spec 2152
    }; 
    
  }
);

DU = (
    {
    DU_INTERFACE_NAME_FOR_F1U           = \"lo\";
    DU_IPV4_ADDRESS_FOR_F1U             = \"127.0.0.1/16\";
    DU_PORT_FOR_F1U                     = 22100;
    F1_U_DU_TRANSPORT_TYPE		    = \"TCP\";
    }
    );
    
CU = (
    {     
        CU_INTERFACE_NAME_FOR_F1U           = \"lo\";
        CU_IPV4_ADDRESS_FOR_F1U             = \"127.0.0.1\";   //Address to search the DU
        CU_PORT_FOR_F1U                     = 22100;
        F1_U_CU_TRANSPORT_TYPE              = \"TCP\";	     // One of TCP/UDP/SCTP
        DU_TYPE 			    = \"LTE\";
    }//,
//    {     
//        CU_INTERFACE_NAME_FOR_F1U           = \"eth0\";
//        CU_IPV4_ADDRESS_FOR_F1U             = \"10.64.93.142\";   //Address to search the DU
//        CU_PORT_FOR_F1U                     = 2211;
//        F1_U_CU_TRANSPORT_TYPE              = \"TCP\";          // One of TCP/UDP/SCTP
//        DU_TYPE 			    = \"WiFi\";
//    }
    );

    CU_BALANCING = \"ALL\";

MACRLCs = (
    {
    num_cc = 1;
    tr_s_preference = \"local_L1\";
    tr_n_preference = \"local_RRC\";
    phy_test_mode = 0;
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
         bands          = [7];
         max_pdschReferenceSignalPower = -27;
         max_rxgain                    = 125;
         eNB_instances  = [0];

    }
);  

NETWORK_CONTROLLER :
{
    FLEXRAN_ENABLED        = \"no\";
    FLEXRAN_INTERFACE_NAME = \"lo\";
    FLEXRAN_IPV4_ADDRESS   = \"127.0.0.1\";
    FLEXRAN_PORT           = 2210;
    FLEXRAN_CACHE          = \"/mnt/oai_agent_cache\";
    FLEXRAN_AWAIT_RECONF   = \"no\";
};

THREAD_STRUCT = (
  {
    #three config for level of parallelism \"PARALLEL_SINGLE_THREAD\", \"PARALLEL_RU_L1_SPLIT\", or \"PARALLEL_RU_L1_TRX_SPLIT\"
    parallel_config    = \"PARALLEL_RU_L1_TRX_SPLIT\";
    #two option for worker \"WORKER_DISABLE\" or \"WORKER_ENABLE\"
    worker_config      = \"WORKER_ENABLE\";
  }
);

     log_config :
     {
       global_log_level                      =\"info\";
       global_log_verbosity                  =\"medium\";
       hw_log_level                          =\"error\";
       hw_log_verbosity                      =\"medium\";
       phy_log_level                         =\"error\";
       phy_log_verbosity                     =\"medium\";
       mac_log_level                         =\"error\";
       mac_log_verbosity                     =\"medium\";
       rlc_log_level                         =\"error\";
       rlc_log_verbosity                     =\"medium\";
       pdcp_log_level                        =\"error\";
       pdcp_log_verbosity                    =\"medium\";
       rrc_log_level                         =\"info\";
       rrc_log_verbosity                     =\"medium\";
    };

mi_log_level:
  {
$mi_string
  };
        ";
        Storage::disk('local')->put('tmp.conf', $content);
    }
}
