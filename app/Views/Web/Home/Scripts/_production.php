<script type='text/javascript'>
    var s = document.createElement('script');
    s.type = 'text/javascript';
    var v = parseInt(Math.random() * 1000000);
    s.src = 'https://api.gerencianet.com.br/v1/cdn/a06dcd5c0b9b62d5fe520612f2e7f3f6/' + v;
    s.async = false;
    s.id = 'a06dcd5c0b9b62d5fe520612f2e7f3f6';
    if (!document.getElementById('a06dcd5c0b9b62d5fe520612f2e7f3f6')) {
        document.getElementsByTagName('head')[0].appendChild(s);
    };
    $gn = {
        validForm: true,
        processed: false,
        done: {},
        ready: function(fn) {
            $gn.done = fn;
        }
    };
</script>