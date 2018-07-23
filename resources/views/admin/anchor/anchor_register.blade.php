<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>爱看球商城后台</title>
    <!-- Bootstrap -->
    {{--<link href="/css//bootstrap.min.css" rel="stylesheet">--}}
    <link href="/css/admin/toastr.min.css" rel="stylesheet">
    <style>
        /*表单格式汇总*/
        div#Form{
            max-width: 1220px; height: 100%;
            padding: 12px 0; margin: 0 auto;
            position: relative;
        }
        div#Form > div.inner{
            border: 1px solid #ddd; border-radius: 4px;
            background: #fff;
        }
        div#Form > div.inner > div.title{
            height: 40px;
            margin: 0 10px; padding-left: 13px;
            border-bottom: 1px solid #ddd;
            position: relative;
            font-size: 14px; line-height: 40px; color: #A8A8A8;
        }
        div#Form > div.inner > div.title:before{
            width: 6px;
            border-radius: 0 2px 2px 0;
            background: #428bca;
            position: absolute; top: 10px; bottom: 10px; left: -10px;
            content: '';
        }
        div#Form > div.inner > dl{
            padding: 40px 0 10px 110px;
        }
        div#Form > div.inner > dl > dd{
            margin-bottom: 20px;
            position: relative;
        }
        div#Form > div.inner > dl > dd:before{
            width: 70px; height: 40px;
            font-size: 14px; line-height: 40px;
            position: absolute; top: 0;left: 0;
        }
        div#Form > div.inner > dl > dd[type=need]:after{
            height: 40px;
            margin-left: 10px;
            font-size: 14px; line-height: 40px; color: #428bca;
            position: absolute; top: 0; bottom: 0;
            content: '*';
        }
        div#Form > div.inner > dl > dd > input{
            width: 42%; height: 28px;
            margin-left: 70px; padding: 5px 10px 5px 20px;
            background: #FAFAFA;
            border: 1px solid #DDDDDD;
            border-radius: 2px;
            font-size: 14px; line-height: 30px;
        }
        div#Form > div.inner > dl > dd > textarea{
            width: 42%; height: 108px;
            margin-left: 70px; padding: 5px 10px 5px 20px;
            background: #FAFAFA;
            border: 1px solid #DDDDDD;
            border-radius: 2px;
            font-size: 14px; line-height: 30px;
            resize: none;
        }
        div#Form > div.inner > dl > dd > div.error{
            height: 34px;
            background: #fff;
            font-size: 14px; line-height: 34px; color: #428bca;
            position: absolute; left: 70px; right: 0; bottom: -34px; z-index: 10;
        }
        div#Form > div.inner > dl > dd > div.prompt{
            height: 34px;
            background: #fff;
            font-size: 14px; line-height: 34px; color: #A8A8A8;
            position: absolute; left: 70px; bottom: -34px; z-index: 1;
        }
        div#Form > div.inner > div.comfirm{
            height: 40px;
            margin: 0 100px; padding: 30px 0 30px 80px;
            border-top: 1px solid #ddd;
            position: relative;
        }
        div#Form > div.inner > div.comfirm > button{
            width: 200px; height: 40px;
            border-radius: 2px;
            background: #428bca;
            font-size: 14px; color: #fff;
        }
        div#Form > div.inner > div.comfirm > button + button{
            margin-left: 16px;
        }
        div#Form > div.inner > div.comfirm > button:disabled{
            cursor: default;
        }
        div#Form > div.inner > div.comfirm > input[type=checkbox]{
            width: 1px; height: 1px;
            margin-left: 30px;
            position: absolute; top: 50%;
            opacity: 0; filter:Alpha(opacity=0);
        }
        div#Form > div.inner > div.comfirm > label{
            width: 20px; height: 20px;
            margin: -11px 0 0 20px;
            background: #FAFAFA;
            border-radius: 2px; border: 1px solid #DDD;
            position: absolute; top: 50%;
            cursor: pointer;
        }
        div#Form > div.inner > div.comfirm > input[type=checkbox]:checked + label{
            background: no-repeat center #FAFAFA; background-size: 20px;
        }
        div#Form > div.inner > div.comfirm > span{
            padding-left: 52px;
            font-size: 14px;
        }
        div#Form > div.inner > div.comfirm > span > a{
            color: #428bca;
        }
        div#Form > div.inner > div.comfirm > span > a:hover{
            text-decoration: underline;
        }
        div#Form > div.inner > div.comfirm > span > em{
            padding-left: 10px;
            color: #428bca;
        }
        div#Form > div.inner > div.comfirm > span > em:before {
            padding-right: 5px;
            content: '*';
        }
        /*特殊样式*/
        div.inner > dl > dd.verification > input{
            width: 28.6%;
            margin-right: 10px;
        }
        div.inner > dl > dd.verification > button{
            width: 12.4%; height: 40px;
            border-radius: 2px;
            background: #428bca;
            font-size: 14px; line-height: 14px; text-align: center; color: #fff;
        }
        div.inner > dl > dd.verification > button[disabled]{
            opacity: 0.55; filter:Alpha(opacity=55);
            cursor: default;
        }
    </style>
    @yield('css')
</head>
<body>
<div id="Form">
    <div class="inner">
        <div class="title">新建账号</div>
        <dl>
            <dd class="name" type="need"><input id="name" type="text" placeholder="请填写您的主播姓名"><div class="error" style="display:none">-</div></dd>
            <dd class="phone" type="need"><input id="phone" type="number" placeholder="请填写本人的真实手机号"><div class="error" style="display:none">-</div></dd>
            <dd class="password" type="need"><input id="password" type="password" placeholder="请填写不少于6位数的密码"><div class="error" style="display:none">-</div></dd>
            <dd class="repeat" type="need"><input id="re_password" type="password" placeholder="请再输入一次密码"><div class="error" style="display:none">-</div></dd>
        </dl>
        <div class="comfirm">
        <button onclick="register(this);">创建</button>
        </div>
    </div>
</div>
</body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="//cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
{{--<script src="../../js/bootstrap.min.js"></script>--}}
<script src="/js/admin/toastr.min.js"></script>
{{--<link href="../../css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">--}}
{{--<script type="text/javascript" src="../../js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>--}}
{{--<script type="text/javascript" src="../../js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>--}}
<script type="text/javascript" src="{{ asset('/js/sha1.js') }}"></script>
{{--<script type="text/javascript" src="{{ asset('/js/public/public.js') }}"></script>--}}
<script type="text/javascript">

    function register(thisObj) {
        var $this = $(thisObj);

        var phone = $("#phone").val();
        var password = $("#password").val();
        var re_password = $("#re_password").val();
        var name = $("#name").val();

        if ($.trim(name) == "") {
            toastr.error("主播名字不能为空");
            return;
        }

        if (!/^1[2-9][0-9]{9}$/.test(phone)) {
            toastr.error("请填写正确的手机号码");
            return;
        }

        if ($.trim(password) == "") {
            toastr.error("请填写密码");
            return;
        }

        if (password.length < 6) {
            toastr.error("密码不能少于6位");
            return;
        }

        if (password != re_password) {
            toastr.error("两次输入的密码不一致");
            return;
        }

        $this.attr("disabled", "disabled");
        var postData = {
            "_token":'{{csrf_token()}}',
            "phone": phone,
            "password": hex_sha1(password),
            "re_password": hex_sha1(re_password),
            "name": name,
        };
        $.ajax({
            url: "/admin/anchor/create",
            data: postData,
            dataType: "json",
            type: "post",
            success: function (json) {
                if (json && json.code == 0) {
                    toastr.success("创建成功");
                    window.close();
                } else if (json) {
                    toastr.error(json.msg);
                } else {
                    toastr.error("创建失败");
                }
                $this.removeAttr("disabled");
            },
            error: function () {
                toastr.error("创建出错");
                $this.removeAttr("disabled");
            }
        });
    }
</script>
</html>
