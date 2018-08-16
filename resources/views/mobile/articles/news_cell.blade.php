@foreach($page as $article)
    <a href="/m{{$article->getUrl()}}" class="li">
        @if(!empty($article->cover))
            <div class="imgbox" style="background: url({{$article->getCover()}}) no-repeat center; background-size: cover;"></div>
        @endif
        <h6>{{$article->title}}</h6>
        <p class="info">{{substr($article->publish_at, 0, 16)}}</p>
    </a>
@endforeach