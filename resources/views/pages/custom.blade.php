@extends('layouts.app', ['activePage' => 'custom', 'titlePage' => __('Custom')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-9 col-md-6 col-sm-6">
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
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('material') }}/js/custom.js"></script>
@endpush