@extends("pc.layout.anchor_base")
@section("css")
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/news.css?rd=2000000003">
@endsection
@section('h1')
	<h1>体育新闻资讯</h1>
@endsection
@section("content")
	<div id="Content">
		<div class="inner">
			<div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<span class="on">资讯</span></div>
			<dl id="Right">
				<dt>直播赛程</dt>
			</dl>
			<div id="Left">
				<div class="article">
					@foreach($articles as $article)
					<div class="con">
						@if(!empty($article->cover))<a href="{{$article->getUrl()}}" class="img" style="background: url({{$article->getCover()}}) no-repeat center; background-size: cover;"></a>@endif
						<a href="{{$article->getUrl()}}" target="_blank" class="title">{{$article->title}}</a>
						<a href="{{$article->getUrl()}}" target="_blank" class="introduction">{{$article->digest}}</a>
						{{--<p class="tag"><span>{{$article->author}}</span><span>{{substr($article->publish_at, 0, 11)}}</span></p>--}}
					</div>
					@endforeach
				</div>
				@if($articles->lastPage() > 1)
				@component("pc.article.news_page_cell", ['lastPage'=>$articles->lastPage(), "curPage"=>$articles->currentPage()]) @endcomponent
				@endif
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










