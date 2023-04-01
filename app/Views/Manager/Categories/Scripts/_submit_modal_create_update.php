<script>
    $('#categories-form').submit(function(e) {

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
                    toastr.error('Verifique os erros e tente novamente');
                    $.each(response.errors, function(field, value){
                        console.log(field);
                        $(form).find('span.' + field).text(value);
                    });  
                    return;
                }

                // Tudo certo.
                toastr.success(response.message);
                $('#categoryModal').modal('hide');
                $(form)[0].reset();
                $("#dataTable").DataTable().ajax.reload(null, false);
                $('.modal-title').text('Criar categoria'); //mudaremos depois com o lang

                $(form).attr('action', '<?php echo route_to('categories.create'); ?>');
                $(form).find('input[name="id"]').val('');
                $('input[name="_method"]').remove();

            },
            error: function() {
                alert('Error backend');
            }
        });
    });
    
    $(document).on('click', '#btnCancel', function() {
        $('input[name="_method"]').remove();
    });
</script>