@if($lastPage > 1)
<div class="page">
    <?php
        $index = 0; $showBtn = 4;
        if ($lastPage - $curPage <= 3) {
            $index = $lastPage - $showBtn;
        } else {
            $index = $curPage - 3;
        }
        $index = $index <= 1 ? 2 : $index;
    ?>
    @if($lastPage > 7 && $curPage != 1)
        <a class="up" href="{{$leaguePath}}news/{{$curPage - 1 == 1 ? '' : ('index'. ($curPage - 1) . '.html' )}}">上一页</a>
    @endif
    <a {{($curPage == 1 ? 'class=on' : 'href=/news/')}} >1</a>
    @if($index > 2) <a>...</a> @endif
    @for($f_index = 0; $f_index < $showBtn; $f_index++)
        @continue($index >= $lastPage)
        <a {{$curPage == $index ? 'class=on' : 'href='.$leaguePath.'news/index' . $index . '.html'}}>{{$index}}</a>
        <?php $index++; ?>
    @endfor
    @if($index < $lastPage) <a>...</a> @endif
    <a {{$curPage == $lastPage ? 'class=on' : 'href='.$leaguePath.'news/index' . $lastPage . '.html'}}>{{$lastPage}}</a>
    {{--<p><span>{{$curPage}}</span>/{{$lastPage}}</p>--}}
    @if ($lastPage > $showBtn && $curPage != $lastPage) <a class="down" href="{{$leaguePath}}news/index{{$curPage + 1}}.html" >下一页</a> @endif
</div>
@endif