<div class="ind_box">
    <?php $index = 0;?>
    @foreach($articles as $article)
        <a href="{{$article['url']}}" class="img_news">
            <img src="{{$article['cover']}}">
            <p>{{$article['title']}}</p>
        </a>
        <?php $index++;?>
        @if($index > 0) @break @endif
    @endforeach
    <div class="text_news">
        <?php $index = 0;?>
        @foreach($articles as $article)
            @if($index > 0)
                <a href="{{$article['url']}}"><span>{{date('m-d H:i', strtotime($article['publish_at']))}}</span>{{$article['title']}}</a>
            @endif
            <?php $index++;?>
        @endforeach
    </div>
</div>