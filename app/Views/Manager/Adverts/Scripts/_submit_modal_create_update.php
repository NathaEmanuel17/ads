<script>
    $('#adverts-form').submit(function(e) {

        e.preventDefault();
        var form = this;

        $.ajax({
            url:$(form).attr('action'),
            method:$(form).attr('method'),
            data: new FormData(form),
            processData:false,
            dataType:'JSON',
            contentType:false,
            beforeSend:function() {
                $(form).find('span.error-text').text('');
            },
            success: function(response) {
                window.refreshCSRFToken(response.token);
                if(response.success == false) {

                    //Erro
                    toastr.error('<?php echo lang('App.danger_validations')?>');
                    $.each(response.errors, function(field, value){
                        console.log(field);
                        $(form).find('span.' + field).text(value);
                    });  
                    return;
                }

                // Tudo certo.
                toastr.success(response.message);
                $('#modalAdvert').modal('hide');
                $(form)[0].reset();
                $("#dataTable").DataTable().ajax.reload(null, false);

                $('.modal-title').text('<?php echo lang('Adverts.title_new'); ?>');

                $(form).find('input[name="id"]').val('');
            },
            error: function() {
                alert('Error backend');
            }
        });
    });
    
</script>