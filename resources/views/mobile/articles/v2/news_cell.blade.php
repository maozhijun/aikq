@foreach($page as $article)
    @include('mobile.articles.v2.news_item_cell', ['article'=>$article])
@endforeach