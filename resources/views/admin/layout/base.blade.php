<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>爱看球管理后台</title>
    <!-- Bootstrap -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/toastr.js/latest/css/toastr.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.svg" rel="stylesheet">
    <link href="{{ asset('/css/admin/dashboard.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>

@yield('content')

</body>
<script src="//cdn.bootcss.com/jquery/2.1.4/jquery.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.js"></script>
<script src="//cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
<script src="//cdn.bootcss.com/js-sha1/0.4.1/sha1.js"></script>
<script type="text/javascript">
    $(function () {
        var success = '{{ session('success','') }}';
        var error = '{{ session('error','') }}';
        if (success != '') {
            toastr.success(success);
        } else if (error != '') {
            toastr.error(error);
        }

        var path = "/{{request()->path()}}";
        var curA = $("ul.nav-sidebar li a[href^='" + path + "']:first");
        curA.parent().addClass("active");
    });
</script>
@yield('js')
</html>
