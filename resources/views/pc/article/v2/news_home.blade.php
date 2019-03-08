@extends("pc.layout.v2.base")
@section("css")
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/news_list_2.css?201903071908}}">
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?201903071908}}">
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
			<div class="con_box" style="">
				<div class="header_con">
					<h4>最近直播</h4>
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
			<div class="con_box">
				<div class="header_con">
					@if(isset($zhuanti))
						<h4>最新{{$zhuanti['name']}}视频</h4>
						<a href="/{{$zhuanti['name_en']}}/video/">{{$zhuanti['name']}}视频集锦</a>
						<a href="/{{$zhuanti['name_en']}}/record/">{{$zhuanti['name']}}比赛录像</a>
					@else
						<h4>最新视频</h4>
						<a href="/video/">视频集锦</a>
						<a href="/record/">比赛录像</a>
					@endif
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
@endsection
@section('js')
	<script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/news_list_2.js"></script>
	<script type="text/javascript">
        var LeagueKeyword = '{{isset($zhuanti) ? $zhuanti['name_en'] : 'all'}}';
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
	</script>
@endsection










