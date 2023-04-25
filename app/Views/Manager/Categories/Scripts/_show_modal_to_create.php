<script>
    $(document).on('click', '#createCategoryBtn', function() {

        $('input[name="_method"]').remove();

        $('.modal-title').text('Criar categoria'); //mudaremos depois com o lang
        $('#categoryModal').modal('show');
        $('input[name="id"]').val('');
        $('input[name="_method"]').remove('');
        $('#categories-form')[0].reset();
        $('#categories-form').attr('action', '<?php echo route_to('categories.create'); ?>');
        $('#categories-form').find('span.error-text').text('');

        var url = '<?php echo route_to('categories.parents'); ?>';

        $.get(url , function(response) {

            $('#boxParents').html(response.parents);

        }, 'json');
    })

    $(document).on('click', '#btnCancel', function() {
        $('input[name="_method"]').remove();
    })
</script>