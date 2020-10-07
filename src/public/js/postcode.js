$('#postcode').on('blur', function () {
    let postcode = $(this).val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('app-content')},
        url: urlPostcode,
        data: {'postcode': postcode, '_token': token, 'base': 'site'},
        type: 'post',
        dataType: 'json',
        success: function (json) {
            if (json.logradouro) {
                $('#street').val(json.logradouro);
                $('#neighborhood').val(json.bairro);
                $('#city').val(json.cidade);
                $('#uf').val(json.estado);
                $('#number_home').focus();
            } else {
                $('#street').focus();
            }
        },
        error: function (error, errorText) {
            console.log(error, errorText);
        },
    });
});
