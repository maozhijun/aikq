<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta charset="UTF-8">
	<title>爱看球主播后台</title>
	<link rel="stylesheet" type="text/css" href="/backstage/css/style.css">
	<link rel="stylesheet" type="text/css" href="/backstage/css/login.css">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta http-equiv="X-UA-Compatible" content="edge" />
	<meta name="renderer" content="webkit|ie-stand|ie-comp">
	<meta name="baidu-site-verification" content="nEdUlBWvbw">
	<link rel="Shortcut Icon" data-ng-href="/img/pc/ico.ico" href="/img/pc/ico.ico">
</head>
<body>
	<div id="Navigation">
		<div class="inner"></div>
	</div>
	<div id="Login">
		<div class="login">
			<form action="/bs/login" method="post" onsubmit="return formSubmit(this);">
				<input type="hidden" name="_token" value="{{csrf_token()}}"/>
				<input type="hidden" name="target" value="{{request('target_url')}}"/>
				<input type="number" name="phone" placeholder="输入您的账号" value="{{session("phone")}}">
				<input type="password" name="password" placeholder="输入您的密码">
				<button type="submit">登录</button>
			</form>
		</div>
	</div>
</body>
<script type="text/javascript" src="/backstage/js/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="/backstage/js/jquery_191.js"></script>
<![endif]-->
<!-- <script type="text/javascript" src="js/home.js"></script> -->
<script src="//cdn.bootcss.com/js-sha1/0.4.1/sha1.js"></script>
<script type="text/javascript">
	window.onload = function () { //需要添加的监控放在这里
		
	}
    function formSubmit(form) {
		var phone = form.phone.value;
		var password = form.password.value;
		if (!/\d{11}/.test(phone)) {
		    alert("账号为手机号码，请填写正确的手机号码");
		    return false;
		}
		if ($.trim(password) == "") {
            alert("请输入密码");
            return false;
		}
        form.password.value = sha1(form.password.value);
        return true;
    }
    var error = "{{session('error')}}";
    var success = "{{session("success")}}";
    if (error != "") {
        alert(error);
	} else if (success != "") {
        alert(success);
	}
</script>
</html>













