<div class="news_list_con news" style="display: none;">
    @if(isset($articles) && count($articles) > 0)
        @foreach($articles as $article)
            @include('mobile.articles.v2.news_item_cell', ['article'=>$article])
        @endforeach
    @endif
</div>