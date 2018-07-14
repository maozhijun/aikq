@extends("backstage.layout.nav")
@section("css")
	<link rel="stylesheet" type="text/css" href="/backstage/css/info.css">
@endsection
@section("content")
	<div id="Content">
		<div class="inner">
			<div id="Tab">
				<a class="on">直播信息</a>
				<a href="match.html">赛事预约</a>
			</div>
			<div id="Link">
				<!-- <button class="get">开始直播，获取推流地址</button> -->
				<button class="reset">重置推流地址</button>
				<button class="end">结束直播</button>
				<div class="link">
					<input type="text" name="link" value="http://www.baidu.com">
					<button class="copy">复制</button>
				</div>
			</div>
			<div class="box">
				<p class="title">主播信息</p>
				<div class="item">
					<p class="name">主播昵称：</p>
					<input type="text" name="nickname" placeholder="请输入您的昵称" value="{{$anchor->name}}">
				</div>
				<div class="item">
					<p class="name">主播头像：</p>
					<img src="{{empty($anchor->icon) ? 'img/image_avatar_n.jpg' : $anchor->icon}}" class="face">
					<button>上传</button>
					<div class="prompt">
						<p>*请上传正方形头像</p>
						<p>*图片大小不得大于<b>1M</b></p>
					</div>
				</div>
			</div>
			<div class="box">
				<p class="title">主播间信息</p>
				<div class="item">
					<p class="name">主播间标题：</p>
					<input type="text" name="roomname" placeholder="请输入房间标题">
				</div>
				<div class="item">
					<p class="name">主播间封面：</p>
					<img src="img/image_picture_n.jpg" class="cover">
					<button>上传</button>
					<div class="prompt">
						<p>*图片大小不得大于<b>1M</b></p>
					</div>
				</div>
			</div>
			<div class="comfirm">
				<button>保存</button>
			</div>
		</div>
	</div>
@endsection














