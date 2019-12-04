@extends('layouts.app', ['activePage' => 'oai', 'titlePage' => __('Oai')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
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
                                <button type="button" class="btn btn-warning" id="conf_sys" name="conf_sys" data-toggle="modal" data-target="#Modal1">
                                    System Configuration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-danger card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">info_outline</i>
                            </div>
                            <p class="card-category">MI Status</p>
                            <h4 class="card-title">{{ $mi_config }}</h4>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <button type="button" class="btn btn-danger" id="confBtn1" name="confBtn1" data-toggle="modal" data-target="#Modal1">
                                    MI Configuration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-info card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">build</i>
                            </div>
                            <p class="card-category">OAI Status</p>
                            <h4 class="card-title">
                                {{$oai_status['status']}}
                                @if ($oai_status['status'] === "On")
                                    {{ " for " . $oai_status['time'] }}
                                @endif
                            </h4>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <button type="button" class="btn btn-warning" id="confBtn1" name="confBtn1" data-toggle="modal" onclick="Run();" >
                                    Start
                                </button>
                                <button type="button" class="btn btn-disabled" id="confBtn1" name="confBtn1" data-toggle="modal" data-target="#Modal1">
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
                    <button type="button" class="btn btn-success" id="confBtn1">
                        <a href="log/log.txt" download="log.txt">
                        Download
                        </a>
                    </button>
                </nav>
                <div class="scroll", style="text-align: center; overflow-y: scroll; width: 100%; height: 400px; background:#FFF; color:#000;">
                    <p id="showResult1"></p>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="Modal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configuration --- Module 1</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align: left">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" name = "Sumbitbtn1" class="btn btn-primary" onclick="writeConfig1();" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        var timerId1 = -1;

        $(document).ready(function() {
            // Javascript method's body can be found in assets/js/demos.js
            md.initDashboardPageCharts();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function read1() {
            $.ajax({
                type: "POST",
                url: "/oai/start",
                data: {'readSign1':"true"},
                dataType: 'json',
                success: function(returndata){
                    console.log(returndata);
                    $('#showResult1').html(returndata)
                    //alert("success!");
                },
                error: function(xhr, status, error){
                    alert("error!");
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                }
            });
        }
        function Run() {
            $('#conf_sys').prop("disabled",false);
            $('#conf_mi').prop("disabled",false);

            $.ajax({
                type: "POST",
                url: "/oai/start",
                data: {'runSign1':"true"},
                dataType: 'json',
                success: function(returndata){
                },
                error: function(xhr, status, error){
                    alert("error after running!");
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                }
            });
            timerId1 = setInterval("read1()",1000);
        }
    </script>
@endpush