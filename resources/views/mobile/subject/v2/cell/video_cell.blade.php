<div class="video_list_con video" style="display: none;">
    @if(isset($videos) && count($videos) > 0)
        @foreach($videos as $video)
            @include('mobile.cell.v2.video_item_cell', ['video'=>$video])
        @endforeach
    @endif
</div>