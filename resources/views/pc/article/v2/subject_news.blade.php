<?php
$leagueName = isset($zhuanti) ? $zhuanti['name'] : "其他";
$leagueNameLong = isset($zhuanti) ? $zhuanti['name_long'] : "其他";
$leaguePath = isset($zhuanti) ? '/'.$zhuanti['name_en'].'/' : "/";
$leagueIcon = isset($zhuanti) ? $zhuanti['icon'] : "";
$leagueTeamCount= isset($zhuanti) ? $zhuanti['team_count'] : 0;
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
				<img src="{{$leagueIcon}}">
				<h3>{{$leagueNameLong}}</h3>
				<p>球队：<span>{{$leagueTeamCount}}支</span></p>
			</a>
			<div class="con_box">
				<div class="header_con">
					<h4>最近{{$leagueName}}直播</h4>
					<a href="/">全部直播</a>
				</div>
				<div class="live">
					@if(isset($combData['matches']))
						@foreach($combData['matches'] as $match)
							@include('pc.cell.v2.right_match_cell', ['match'=>$match])
						@endforeach
					@endif
				</div>
			</div>
			<div class="con_box" style="">
				<div class="header_con">
					<h4>最新{{$leagueName}}视频</h4>
					<a href="{{$leaguePath}}">{{$leagueName}}视频集锦</a>
					<a href="{{$leaguePath}}">{{$leagueName}}比赛录像</a>
				</div>
				<div class="video">
					@if(isset($combData['videos']))
						@foreach($combData['videos'] as $video)
							<div class="video_item">
								<a href="{{$video['link']}}">
									<p class="img_box"><img src="{{$video['image']}}"></p>
									<p class="text_box">{{$video['title']}}</p>
								</a>
							</div>
						@endforeach
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection










