@if($lastPage > 1)
    <?php
        $index = 0; $showBtn = 7;
        if ($lastPage - $curPage <= 3) {
            $index = $lastPage - $showBtn;
        } else {
            $index = $curPage - 3;
        }
        $index = $index <= 1 ? 2 : $index;
    ?>
    @if($lastPage > 7 && $curPage != 1)
        <a class="up" href="/live/subject/videos/{{$type}}/{{$curPage - 1}}.html">上一页</a>
    @endif
    <a {{($curPage == 1 ? 'class=on' : 'href=/live/subject/videos/' . $type . '/1.html')}} >1</a>
    @if($index > 2) <p>...</p> @endif
    @for($f_index = 0; $f_index < $showBtn; $f_index++)
        @continue($index >= $lastPage)
        <a {{$curPage == $index ? 'class=on' : 'href=/live/subject/videos/' . $type . '/' . $index . '.html'}}>{{$index}}</a>
        <?php $index++; ?>
    @endfor
    @if($index < $lastPage) <p>...</p> @endif
    <a {{$curPage == $lastPage ? 'class=on' : 'href=/live/subject/videos/' . $type . '/' . $lastPage . '.html'}}>{{$lastPage}}</a>
    <p><span>{{$curPage}}</span>/{{$lastPage}}</p>
    @if ($lastPage > 7 && $curPage != $lastPage) <a class="down" href="/live/subject/videos/{{$type}}/{{$curPage + 1}}.html" >下一页</a> @endif
@endif