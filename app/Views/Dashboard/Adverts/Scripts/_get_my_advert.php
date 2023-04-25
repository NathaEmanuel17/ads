<script>
    $(document).on('click', '#btnEditAdvert', function() {

        var id = $(this).data('id');
        var url = '<?php echo route_to('get.my.advert'); ?>';

        $.get(url, {

            id: id
        }, function(response) {

            $('#modalAdvert').modal('show');

            $('.modal-title').text('<?php echo lang('Adverts.title_edit'); ?>' + response.advert.code);

            // $('#categories-form').attr('action', '<?php echo route_to('categories.update'); ?>');
            // $('#categories-form').find('input[name="id"]').val(response.category.id);
            // $('#categories-form').find('input[name="name"]').val(response.category.name);
            // $('#categories-form').append("<input type='hidden' name='_method' value='PUT'>");
            // $('#boxParents').html(response.parents);
            // $('#categories-form').find('span.error-text').text('');

        }, 'json');


        // $('input[name="id"]').val(''); // limpamos o id
        // $('input[name="_method"]').remove(''); // removemos o spoofing
        // $('#adverts-form')[0].reset();
        // $('#adverts-form').attr('action', '<?php echo route_to('adverts.create'); ?>');
        // $('#adverts-form').find('span.error-text').text('');

        // TODO: fazer o ajax request para buscar as situações e categorias
    });
</script>