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
                $('#download_button').addClass('disabled');
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

