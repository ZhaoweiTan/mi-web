$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function submit() {
    var form = document.getElementById('file_form');
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
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
