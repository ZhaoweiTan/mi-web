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
                    <form id="config_form">
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
                <div class="modal-footer">
                    <button type="button" name="Sumbitbtn1" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Javascript method's body can be found in assets/js/demos.js
            md.initDashboardPageCharts();
            get_status();
            setInterval("get_status()",5000);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function get_status() {
            $.ajax({
                type: "GET",
                url: "/oai/check_status",
                data: {},
                dataType: 'json',
                success: function(returndata){
                    if (returndata == "On") {
                        // green
                        $('#oai_status').html(returndata);
                        $('#oai_status').css('color', 'green');
                        $('#start_button').prop('disabled', true);
                        $('#start_button').addClass('btn-disabled');
                        $('#start_button').removeClass('btn-info');
                        $('#stop_button').prop('disabled', false);
                        $('#stop_button').addClass('btn-info');
                        $('#stop_button').removeClass('btn-disabled');
                        read();
                    } else {
                        // green
                        $('#oai_status').html(returndata);
                        $('#oai_status').css('color', "red");
                        $('#start_button').prop('disabled', false);
                        $('#start_button').addClass('btn-info');
                        $('#start_button').removeClass('btn-disabled');
                        $('#stop_button').prop('disabled', true);
                        $('#stop_button').addClass('btn-disabled');
                        $('#stop_button').removeClass('btn-info');
                    }
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                }
            });
        }

        function read() {
            $.ajax({
                type: "POST",
                url: "/oai/read",
                data: {'keyword': document.getElementById("filter").value},
                dataType: 'json',
                success: function(returndata){
                    $('#showResult').html(returndata)
                    $('#log_scroll').scrollTop($('#log_scroll').prop('scrollHeight'));
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                }
            });
        }

        function kill() {
            $.ajax({
                type: "POST",
                url: "/oai/kill",
                data: {},
                dataType: 'json',
                success: function(returndata){
                    get_status();
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                }
            });
        }

        function start_oai() {
            $('#conf_sys').prop("disabled",false);
            $('#conf_mi').prop("disabled",false);

            $.ajax({
                type: "POST",
                url: "/oai/start",
                data: {
                    'config_mi': $('#config_form').serializeArray(),
                },
                dataType: 'json',
                success: function(returndata) {
                    get_status();
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                }
            });
        }



    </script>
@endpush