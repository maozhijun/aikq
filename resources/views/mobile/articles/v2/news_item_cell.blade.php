<a href="{{$article->getUrl()}}">
    @if(!empty($article->cover))
        <p class="img_box" style="background-image: url({{$article->getCover()}})"></p>
    @endif
    <h3>{{$article->title}}</h3>
    <p class="date_con">{{date('m-d', strtotime($article->publish_at))}}</p>
    <p class="tag_con">
        @foreach(explode(',', $article->labels) as $tag)
            <span>{{$tag}}</span>
        @endforeach
    </p>
</a>