@extends("pc.layout.v2.base")
@section("css")
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/news_list_2.css?{{date('YmdHi')}}">
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?{{date('YmdHi')}}">
@endsection
@section('h1')
	<h1>体育新闻资讯</h1>
@endsection
@section("content")
	<div class="def_content" id="Part_parent">
		<div id="Left_part">
			<div class="el_con">
				<div class="header">
					<h3><p>最新</p></h3>
				</div>
				<div class="list_con">
					@foreach($lastArticles as $articles)
						@include('pc.article.v2.news_home_item_cell', ['articles'=>$articles])
					@endforeach
				</div>
			</div>
			@foreach($leagues as $league)
				<div class="el_con">
					<div class="header">
						<h3><p>{{$league['name']}}</p></h3>
						<p class="aline">
							<a href="/{{$league['name_en']}}/news/">更多{{$league['name']}}资讯 ></a>
						</p>
					</div>
					<div class="list_con">
						@include('pc.article.v2.news_home_item_cell', ['articles'=>$league['articles']])
					</div>
				</div>
			@endforeach
		</div>
		<div id="Right_part">
			<div class="con_box">
				<div class="header_con">
					<h4>最近直播</h4>
					<a href="/">全部直播</a>
				</div>
				<div class="live">
					<div class="live_item">
						<p class="live_match_info">NBA<span>01-24 16:20</span></p>
						<div class="live_match_team">
							<p class="team"><span><a href="team.html">达拉斯独行侠</a></span></p>
							<p class="vs"><span>直播中</span></p>
							<p class="team"><span><a href="team.html">多伦多猛龙</a></span></p>
						</div>
						<div class="live_match_line">
							<a href="live.html">高清直播</a>
							<a href="live.html">主播剧本球童</a>
							<a href="live.html">体育直播</a>
						</div>
					</div>
				</div>
			</div>
			<div class="con_box">
				<div class="header_con">
					<h4>最新视频</h4>
					<a href="video_list.html">视频集锦</a>
					<a href="record_list.html">比赛录像</a>
				</div>
				<div class="video">
					<div class="video_item">
						<a href="video.html">
							<p class="img_box"><img src="https://puui.qpic.cn/vpic/0/j0839gwmhv0.png/0"></p>
							<p class="text_box">直击-哈登赛后采访挤爆</p>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section("js")
	<script type="text/javascript">
        $.get("/news/lives.html", function (html) {
            $("#Right").html(html);
        });
	</script>
@endsection










