<script>
    $(document).on('click', '#createPlanBtn', function() {

        $('input[name="_method"]').remove();
        
        $('.modal-title').text('<?php echo lang('Plans.title_new'); ?>');
        $('#modalPlan').modal('show');

        $('#plans-form')[0].reset();
        $('#plans-form').attr('action', '<?php echo route_to('plans.create'); ?>');
        $('#plans-form').find('span.error-text').text('');

        var url = '<?php echo route_to('plans.get.recorrences'); ?>';

        $.get(url , function(response) {

            $('#boxRecorrences').html(response.recorrences);

        }, 'json');
    })

</script>