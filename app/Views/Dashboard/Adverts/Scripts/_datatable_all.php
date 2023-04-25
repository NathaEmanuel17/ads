<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pagingType"  : 'numbers',
            "order"       : [],
            "deferRender" : true,
            "processing"  : true,
            "responsive"  : true,        
            ajax: '<?php echo route_to('get.all.my.Adverts')?>',
            columns: [{
                    data:  'image'  
                },
                {
                    data: 'title'
                },
                {
                    data: 'code'
                },
                {
                    data: 'category'
                },
                {
                    data: 'is_published'
                },
                {
                    data: 'address'
                },
                {
                    data: 'actions'
                },
                
            ],
        });
    });
</script>
