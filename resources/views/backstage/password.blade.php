@extends("backstage.layout.nav")
@section("css")
	<link rel="stylesheet" type="text/css" href="/backstage/css/login.css">
@endsection
@section("content")
	<div id="Login">
		<div class="password">
		<form method="post" action="/bs/password/edit" onsubmit="return formSubmit(this);">
			<input type="hidden" name="_token" value="{{csrf_token()}}"/>
			<p class="title">修改密码</p>
			<div class="box">
				<p>当前密码：</p>
				<input type="password" name="old" placeholder="输入当前密码">
			</div>
			<div class="box">
				<p>新密码：</p>
				<input type="password" name="new" placeholder="输入新密码">
			</div>
			<div class="box">
				<p>确认密码：</p>
				<input type="password" name="copy" placeholder="再次输入新密码">
			</div>
			<div class="box" type="submit">
				<button>确认修改</button>
			</div>
		</form>
		</div>
	</div>
@endsection
@section("js")
	<script type="text/javascript" src="//cdn.bootcss.com/js-sha1/0.4.1/sha1.js"></script>
	<script type="text/javascript">
        function formSubmit(form) {
            var old = form.old.value;
            var newP = form.new.value;
            var copyP = form.copy.value;

            if ($.trim(old) == "") {
                alert("请输入当前密码");
                return false;
            }
            if ($.trim(newP) == "") {
                alert("请输入新密码");
                return false;
            }
            if ($.trim(newP).length < 6) {
                alert("密码不能少于6位字符");
                return false;
            }
            if ($.trim(copyP) == "") {
                alert("再次输入新密码");
                return false;
            }
            if (newP != copyP) {
                alert("两次输入的密码不一致");
                return false;
			}

            form.old.value = sha1(old);
            form.new.value = sha1(newP);
            form.copy.value = sha1(copyP);
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
@endsection












