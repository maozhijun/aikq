<a href="{{$article['link']}}">
    @if(!empty($article['cover']))
        <p class="img_box" style="background-image: url({{$article['cover']}})"></p>
    @endif
    <h3>{{$article['title']}}</h3>
    <p class="date_con">{{date('m-d', strtotime($article['update_at']))}}</p>
    <p class="tag_con">
        @foreach($article['tags'] as $tag)
            <span>{{$tag}}</span>
        @endforeach
    </p>
</a>