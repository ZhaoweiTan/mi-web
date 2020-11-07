@extends('layouts.app', ['activePage' => 'custom', 'titlePage' => __('Custom')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-warning card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">backup</i>
                            </div>
                            <p class="card-category">Customized analysis</p>
                            <h4 class="card-title" id="log_string">
                                Upload your MI-eNB log, analyzer, and analysis script to run customized analysis
                            </h4>
                            <button type="button" class="btn btn-disabled" id="run_button" onclick="submit();" disabled>Run Analysis</button>
                        </div>
                        <div class="card-footer">
                            <form class="form-check-inline" id="file_form" action="custom/analysis" method="post">
                                @csrf
                                <div>
                                    Select customized log file:
                                    <input type="file" class="form-control-file file_input" id="uploadLog" name="log_file">
                                </div>
                                <div>
                                    Upload your analyzer:
                                    <input type="file" class="form-control-file file_input" id="uploadAnalyzer" name ="analyzer_file" >
                                </div>
                                <div>
                                    Upload analysis script
                                    <input type="file" class="form-control-file file_input" id="uploadScript" name="script_file">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-info card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">backup</i>
                            </div>
                            <p class="card-category">Customized Co-analysis</p>
                            <h4 class="card-title" id="log_string">
                                Upload your MI-phone log, MI-eNB log, analyzer, and analysis script to run customized analysis
                            </h4>
                            <button type="button" class="btn btn-disabled" id="run_co_button" onclick="submit_co();" disabled>Run Analysis</button>
                        </div>
                        <div class="card-footer">
                            <form class="form-check-inline" id="file_form_co" action="custom/co-analysis" method="post">
                                @csrf
                                <div>
                                    Upload MI Phone Side Log
                                    <input type="file" class="form-control-file file_input_co" id="uploadPhone_co" name="phone_file">
                                </div>
                                <div>
                                    Select customized log file:
                                    <input type="file" class="form-control-file file_input_co" id="uploadLog_co" name="log_file">
                                </div>
                                <div>
                                    Upload your analyzer:
                                    <input type="file" class="form-control-file file_input_co" id="uploadAnalyzer_co" name ="analyzer_file" >
                                </div>
                                <div>
                                    Upload analysis script
                                    <input type="file" class="form-control-file file_input_co" id="uploadScript_co" name="script_file">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="scroll" style="text-align: left; overflow-y: scroll; width: 80%; height: 400px; margin: 0 auto; background:#FFF; color:#000; padding-left: 20px; padding-top: 10px;", id="analysis_scroll">
                        <h4 class="card-title" style="text-align: center;"><i>Analysis Result</i></h4> <hr/>
                        <p id="analysis_result"></p>
                        <div style="text-align: center" id="img_container"> </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="scroll" style="text-align: left; overflow-y: scroll; width: 80%; height: 400px; margin: 0 auto; background:#FFF; color:#000; padding-left: 20px; padding-top: 10px;", id="co_analysis_scroll">
                        <h4 class="card-title" style="text-align: center;"><i>Co-Analysis Result</i></h4> <hr/>
                        <p id="co_analysis_result"></p>
                        <div style="text-align: center" id="co_img_container"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('material') }}/js/custom.js"></script>
@endpush