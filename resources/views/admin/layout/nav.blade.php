<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>料狗CMS</title>
    <!-- Bootstrap -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.svg" rel="stylesheet">
    {{--<link href="//cdn.bootcss.com/toastr.js/latest/css/toastr.css" rel="stylesheet">--}}
    <link href="{{env('CDN_URL')}}/css/admin/toastr.min.css" rel="stylesheet">
    <link href="{{ asset('/css/admin/dashboard.css') }}" rel="stylesheet">
    @yield('css')
    <style>
        .sidebar ul {
            margin-bottom: 0;
        }
    </style>
</head>
<body @yield('body_method')>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/cms/">料狗CMS</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">{{ request()->_account->name }}</a></li>
                <li><a href="/cms/logout/">退出</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php
    //获取有权限的一级目录
    $account = request()->_account;
    $menus = $account->firstMenus();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                @foreach($menus as $menu)
                    <li><a href="{{$menu->action}}">{{$menu->name}}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            @yield('content')
        </div>
    </div>
</div>

@yield('extra_content')

</body>
<script src="//cdn.bootcss.com/jquery/2.1.4/jquery.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.js"></script>
{{--<script src="//cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>--}}
<script src="{{env('CDN_URL')}}/js/admin/toastr.min.js"></script>
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
