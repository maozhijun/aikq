<div class="news_list_con news" style="display: none;">
    @if(isset($articles) && count($articles) > 0)
        @foreach($articles as $article)
            @include('mobile.subject.v2.cell.news_item_subject_cell', ['article'=>$article])
        @endforeach
    @endif
</div>