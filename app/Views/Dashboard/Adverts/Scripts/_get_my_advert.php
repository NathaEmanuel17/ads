<script>
    $(document).on('click', '#btnEditAdvert', function() {

        var id = $(this).data('id');
        var url = '<?php echo route_to('get.my.advert'); ?>';

        $.get(url, {

            id: id
        }, function(response) {

            $('#modalAdvert').modal('show');

            $('.modal-title').text('<?php echo lang('Adverts.title_edit'); ?>' + ' - ' + response.advert.code);

            $('#adverts-form').attr('action', '<?php echo route_to('adverts.update.my'); ?>');
            $('#adverts-form').find('input[name="id"]').val(response.advert.id);
            $('#adverts-form').append("<input type='hidden' name='_method' value='PUT'>");
            $('#boxSituations').html(response.situations);
            $('#boxCategories').html(response.categories);
            $('#adverts-form').find('input[name="title"]').val(response.advert.title);
            $('#adverts-form').find('input[name="price"]').val(response.advert.price);
            $('#adverts-form').find('input[name="zipcode"]').val(response.advert.zipcode);
            $('#adverts-form').find('input[name="street"]').val(response.advert.street);
            $('#adverts-form').find('input[name="number"]').val(response.advert.number);
            $('#adverts-form').find('input[name="neighborhood"]').val(response.advert.neighborhood);
            $('#adverts-form').find('input[name="city"]').val(response.advert.city);
            $('#adverts-form').find('input[name="state"]').val(response.advert.state);
            $('#adverts-form').find('textarea[name="description"]').val(response.advert.description);


            $('#adverts-form').find('span.error-text').text('');

        }, 'json').fail(function() {

            toastr.error("We couldn't find the ad");

        });;


        // $('input[name="id"]').val(''); // limpamos o id
        // $('input[name="_method"]').remove(''); // removemos o spoofing
        // $('#adverts-form')[0].reset();
        // $('#adverts-form').attr('action', '<?php echo route_to('adverts.create'); ?>');
        // $('#adverts-form').find('span.error-text').text('');

        // TODO: fazer o ajax request para buscar as situações e categorias
    });
</script>