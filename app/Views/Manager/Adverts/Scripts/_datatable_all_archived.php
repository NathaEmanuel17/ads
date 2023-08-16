<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pagingType"  : 'numbers',
            "order"       : [],
            "deferRender" : true,
            "processing"  : true,
            "responsive"  : true,        
            ajax: '<?php echo route_to('get.archived.manager.Adverts')?>',
            columns: [
                {
                    data: 'title'
                },
                {
                    data: 'code'
                },
                {
                    data: 'actions'
                },
                
            ],
        });
    });
</script>
