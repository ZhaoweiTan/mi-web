@extends('layouts.app', ['activePage' => 'oai', 'titlePage' => __('Oai')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-warning card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">content_copy</i>
                            </div>
                            <p class="card-category">System Config</p>
                            <h4 class="card-title">{{ $system_config }} </h4>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <button type="button" class="btn btn-warning" id="conf_sys" name="conf_sys" data-toggle="modal" data-target="#config_modal">
                                    System Configuration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-info card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">build</i>
                            </div>
                            <p class="card-category">OAI Status</p>
                            <h4 class="card-title" id="oai_status">
                            </h4>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <button type="button" class="btn" id="start_button" name="confBtn1" data-toggle="modal" onclick="start_oai();" >
                                    Start
                                </button>
                                <button type="button" class="btn" id="stop_button" name="confBtn1" data-toggle="modal" onclick="kill();">
                                    Stop
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <nav id="navbar-example2" class="navbar navbar-light bg-light" style="width: 100%">
                    <a class="navbar-brand" href="#">Execution Log</a>
                    <a class="navbar-brand" href="#">Filter the log by keyword:</a>
                    <input class="form-control" id="filter" placeholder="Keyword (E.g. SCTP)">
                    <button type="button" class="btn" id="download_button" style="margin: auto;">
                        <a href="log/log.txt" download="log.txt">
                        Download This Log
                        </a>
                    </button>
                </nav>
                <div class="scroll", style="text-align: left; overflow-y: scroll; width: 100%; height: 400px; background:#FFF; color:#000; padding-left: 20px; padding-top: 10px;", id="log_scroll">
                    <p id="showResult"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="config_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!--div class="modal-header">
                    <h5 class="modal-title">OAI System Configuration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div-->
                <!--div class="modal-body" style="text-align: left">
                    <form id="form1" method="post">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Log Level</label>
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label" for="inlineRadio11">
                                    <input class="form-check-input" type="radio" name="RadioOptions11" id="inlineRadio11" value="Debug"> Debug
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label" for="inlineRadio11">Info
                                    <input class="form-check-input" type="radio" name="RadioOptions11" value="Info">
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label" for="inlineRadio11">Warning
                                    <input class="form-check-input" type="radio" name="RadioOptions11" value="Warning">
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label" for="inlineRadio11">Error
                                    <input class="form-check-input" type="radio" name="RadioOptions11" value="Error">
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Bandwidth</label>
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label" for="inlineRadio14">5

                                    <input class="form-check-input" type="radio" name="RadioOptions14" id="inlineRadio14" value="5">
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label" for="inlineRadio14">10
                                    <input class="form-check-input" type="radio" name="RadioOptions14" value="10">
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div -->
                <div class="modal-header">
                    <h5 class="modal-title">MobileInsight Log Configuration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align: left">
                    <form id="miconfig_form">
                    @foreach ($mi_array as $k => $v)
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="{{$k}}">
                            {{$v}}
                            <span class="form-check-sign">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                    @endforeach
                    </form>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title">OAI Configuration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align: left">
                    <form id="sysconfig_form">
                        <div>
                            Select Band:
                            @foreach ($oai_status['band'] as $band)
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="config_band" id="inlineRadio1" value="option1"> Band {{$band}}
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div name="btnGroup">
                            Select Bandwidth:
                            @foreach ($oai_status['bandwidth'] as $bandwidth)
                            <div class="form-check form-check-radio form-check-inline">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="config_bandwidth" id="inlineRadio1" value="">  {{$bandwidth}} MHz
                                    <span class="circle">
                                        <span class="check"></span>
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" name="Sumbitbtn1" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('material') }}/js/mi.js"></script>
@endpush