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
}
