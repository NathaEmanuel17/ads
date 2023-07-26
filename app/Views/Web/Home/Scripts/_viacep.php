<script>
    $(document).on('change', '[name="zipcode"]', function() {

        let zipcode = $(this).val();

        if (zipcode.length === 9) {

            zipcode = zipcode.replace('-', '');

            var url = `https://viacep.com.br/ws/${zipcode}/json/`;

            $.get(url, {

            }, function(response) {
               
                $('[name="street"]').val(response.logradouro);
                $('[name="neighborhood"]').val(response.bairro);
                $('[name="city"]').val(response.localidade);
                $('[name="state"]').val(response.uf);

                $('#adverts-form').find('span.error-text').text('');

            }, 'json').fail(function() {

                toastr.error("We were unable to find the zip code provided");
                
            });;
        }
    });
</script>