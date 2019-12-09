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
            } else {
                $('#result_modal').modal('show');
                $('#analysis_result').html(rtn['result']);
                $('#img_container').html('<img id="res_image" style="width:100%" src="mi/result.png" />');
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
