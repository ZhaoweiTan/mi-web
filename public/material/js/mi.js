$(document).ready(function() {
    // Javascript method's body can be found in assets/js/demos.js
    md.initDashboardPageCharts();
    get_status();
    setInterval("get_status()",5000);
    $('#config_band7').attr('checked', 'checked');
    $('#config_bandwidth10').attr('checked', 'checked');
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
                $('#oai_status').html(returndata);
                $('#oai_status').css('color', 'green');
                $('#start_button').prop('disabled', true);
                $('#start_button').addClass('btn-disabled');
                $('#start_button').removeClass('btn-info');
                $('#config_button').prop('disabled', true);
                $('#config_button').addClass('btn-disabled');
                $('#config_button').removeClass('btn-warning');
                $('#stop_button').prop('disabled', false);
                $('#stop_button').addClass('btn-info');
                $('#stop_button').removeClass('btn-disabled');
                $('#download_button').removeClass('disabled');
                $('#mi-div').show();
                read();
                if ($('#real_time_analysis').is(':checked')) {
                    run_analysis();
                }
            } else {
                $('#oai_status').html(returndata);
                $('#oai_status').css('color', "red");
                $('#start_button').prop('disabled', false);
                $('#start_button').addClass('btn-info');
                $('#start_button').removeClass('btn-disabled');
                $('#config_button').prop('disabled', false);
                $('#config_button').addClass('btn-warning');
                $('#config_button').removeClass('btn-disabled');
                $('#stop_button').prop('disabled', true);
                $('#stop_button').addClass('btn-disabled');
                $('#stop_button').removeClass('btn-info');
                if ($('#log_result').is(':empty')) {
                    $('#download_button').addClass('disabled');
                } else {
                    $('#download_button').removeClass('disabled');
                }
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
            $('#log_scroll').scrollTop($('#log_scroll').prop('scrollHeight'));
            $('#log_result').html(returndata);
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
            'miconfig': $('#miconfig_form').serializeArray(),
            'sysconfig': $('#sysconfig_form').serializeArray(),
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

function run_analysis() {
    $('#analysis_button').prop('disabled', true);
    $('#analysis_button').addClass('btn-disabled');
    $('#analysis_button').removeClass('btn-primary');
    $('#analysis_button').html('Processing...');
    var tmp = $('#analysis_form').serializeArray();
    if (tmp.length == 0) {
        alert("Please select the analysis type.");
        return;
    }
    $.ajax({
        type: "POST",
        url: "/oai/analysis",
        data: {
            'type': tmp,
        },
        dataType: 'json',
        success: function(returndata) {
            $('#analysis_button').prop('disabled', false);
            $('#analysis_button').removeClass('btn-disabled');
            $('#analysis_button').addClass('btn-primary');
            $('#analysis_button').html('Run Analysis');

            $('#mi_modal').modal('hide');
            $('#analysis_result').scrollTop($('#log_scroll').prop('scrollHeight'));
            $('#analysis_result').html(returndata);
            $('#img_container').html('<img id="res_image" style="width:100%" src="mi/result.png" />');
            $("#res_image").attr("src", "/mi/result.png?" + new Date().getTime()); // refresh the image cache
        },
        error: function(xhr, status, error){
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            alert('Error - ' + errorMessage);
        }
    });
}

function stop_analysis() {
    alert("MobileInsight analysis has been stopped.");
    $('#real_time_analysis').prop('checked', false);
}

