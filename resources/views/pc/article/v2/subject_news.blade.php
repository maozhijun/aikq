<?php
$leagueName = isset($zhuanti) ? $zhuanti['name'] : "其他";
$leaguePath = isset($zhuanti) ? '/'.$zhuanti['name_en'].'/' : "/";
?>
@extends("pc.layout.v2.base")
@section("css")
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/news_list_2.css?{{date('YmdHi')}}">
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?{{date('YmdHi')}}">
@endsection
@section('h1')
	<h1>体育新闻资讯</h1>
@endsection
@section("content")
	<div id="Crumbs">
		<div class="def_content">
			<a href="/">爱看球</a> - <a href="{{$leaguePath}}">{{$leagueName}}</a> - {{$leagueName}}资讯
		</div>
	</div>
	<div class="def_content" id="Part_parent">
		<div id="Left_part">
			<div class="el_con">
				<div class="header">
					<h3><p>{{$leagueName}}资讯</p></h3>
					<p class="aline">
						<a href="{{$leaguePath}}">进入{{$leagueName}}专区 ></a>
					</p>
				</div>
				<div class="list_con">
					@foreach($articles as $article)
						<a class="news_list_box" href="{{$article->getUrl()}}">
							<p class="img_box"><img src="{{$article->getCover()}}"></p>
							<h2>{{$article->title}}</h2>
							<p class="summary_text">{{$article->digest}}</p>
							<div class="info_text">
								{{date('Y-m-d', strtotime($article->publish_at))}}
								<p class="tag">
									@foreach(explode(',', $article->labels) as $tag)
										<span>{{$tag}}</span>
									@endforeach
								</p>
							</div>
						</a>
					@endforeach
					@if($articles->lastPage() > 1)
						@include("pc.article.v2.news_page_cell", ['leaguePath'=>$leaguePath, 'lastPage'=>$articles->lastPage(), "curPage"=>$articles->currentPage()])
					@endif
				</div>
			</div>
		</div>
		<div id="Right_part">
			<a class="banner_entra" href="{{$leaguePath}}">
				<img src="https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=c95a12f874f0f736d8fe4b07326ed424/3801213fb80e7bec36d92766232eb9389b506b31.jpg">
				<h3>美国男子职业篮球联赛</h3>
				<p>球队：<span>30支</span></p>
			</a>
			<div class="con_box" style="display: none">
				<div class="header_con">
					<h4>最近{{$leagueName}}直播</h4>
					<a href="/">全部直播</a>
				</div>
				<div class="live"></div>
			</div>
			<div class="con_box" style="display: none">
				<div class="header_con">
					<h4>最新{{$leagueName}}视频</h4>
					<a href="{{$leaguePath}}">{{$leagueName}}视频集锦</a>
					<a href="{{$leaguePath}}">{{$leagueName}}比赛录像</a>
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










