$(document).ready(function() {   

    $('#monthSelect').on('change', function() {
        if ($(this).val() !== '') {
            $('#formSelect').submit();
        }
    });

});