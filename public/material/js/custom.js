$(document).ready(function() {
    if ( $("#uploadLog").val() != "" && $("#uploadAnalyzer").val() != "" && $("#uploadScript").val() != "") {
        $('#run_button').prop('disabled', false);
        $('#run_button').addClass('btn-primary');
        $('#run_button').removeClass('btn-disabled');
    }
});



$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function submit() {
    var form = document.getElementById('file_form');
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'json';
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            rtn = xhr.response;
            if (rtn['status'] == 2) {
                alert(rtn['msg']);
            } else if (rtn['status'] == 3) {
                $('#analysis_result').scrollTop($('#log_scroll').prop('scrollHeight'));
                $('#analysis_result').html(rtn['result']);
                $('#img_container').html('');
            }
            else {
                $('#analysis_result').scrollTop($('#log_scroll').prop('scrollHeight'));
                $('#analysis_result').html(rtn['result']);
                $('#img_container').html('<img id="res_image" style="width:100%" src="mi/result.png" />');
                $("#res_image").attr("src", "/mi/result.png?" + new Date().getTime()); // refresh the image cache
            }
        }
    };
    xhr.open('POST', 'custom/analysis', true);
    xhr.send(formData);
}


$('.file_input').on('input', function() {
    if ( $("#uploadLog").val() != "" && $("#uploadAnalyzer").val() != "" && $("#uploadScript").val() != "") {
        $('#run_button').prop('disabled', false);
        $('#run_button').addClass('btn-primary');
        $('#run_button').removeClass('btn-disabled');
    }
});


function submit_co() {
    var form = document.getElementById('file_form_co');
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'json';
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            rtn = xhr.response;
            if (rtn['status'] == 2) {
                alert(rtn['msg']);
            } else if (rtn['status'] == 3) {
                $('#co_analysis_result').scrollTop($('#log_scroll').prop('scrollHeight'));
                $('#co_analysis_result').html(rtn['result']);
                $('#co_img_container').html('');
            } else {
                $('#co_analysis_result').html(rtn['result']);
                $('#co_analysis_result').scrollTop($('#log_scroll').prop('scrollHeight'));
                $('#co_img_container').html('<img id="co_res_image" style="width:100%" src="mi/result.png" />');
                $("#co_res_image").attr("src", "/mi/result.png?" + new Date().getTime()); // refresh the image cache
            }
        }
    };
    xhr.open('POST', 'custom/analysis', true);
    xhr.send(formData);
}


$('.file_input_co').on('input', function() {
    if ( $("#uploadLog_co").val() != "" && $("#uploadAnalyzer_co").val() != "" && $("#uploadScript_co").val() != "" && $("#uploadPhone_co").val() != "" ) {
        $('#run_co_button').prop('disabled', false);
        $('#run_co_button').addClass('btn-primary');
        $('#run_co_button').removeClass('btn-disabled');
    }
});