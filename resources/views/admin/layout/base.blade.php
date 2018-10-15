<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>爱看球管理后台</title>
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/css/admin/toastr.min.css" rel="stylesheet">
    {{--<link href="//cdn.bootcss.com/toastr.js/latest/css/toastr.css" rel="stylesheet">--}}
    <link href="/bootstrap//3.3.7/fonts/glyphicons-halflings-regular.svg" rel="stylesheet">
    <link href="{{ asset('/css/admin/dashboard.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>

@yield('content')

</body>
<script src="/js/jquery.js"></script>
<script src="/bootstrap/js/bootstrap.js"></script>
<script src="/js/admin/toastr.min.js"></script>
<script src="/js/sha1.js"></script>
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
