@extends("pc.layout.anchor_base")
@section("css")
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/news.css">
@endsection
@section("content")
	<div id="Content">
		<div class="inner">
			<dl id="Right">
				<dt>直播赛程</dt>
			</dl>
			<div id="Left">
				<div class="article">
					@foreach($articles as $article)
					<div class="con">
						<a href="{{$article->getUrl()}}" target="_blank" class="title">{{$article->title}}</a>
						<a href="{{$article->getUrl()}}" target="_blank" class="introduction">{{$article->digest}}</a>
						<p class="tag"><span>{{$article->author}}</span><span>{{substr($article->publish_at, 0, 11)}}</span></p>
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










