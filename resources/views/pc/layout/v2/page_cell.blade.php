@if($lastPage > 1)
    <div class="page">
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
            <a class="" href="{{$href}}{{$curPage - 1 == 1 ? '' : ('index'. ($curPage - 1) . '.html' )}}">上一页</a>
        @endif
        <?php
        $tmp = $href;
        if (substr($tmp,-1,1) == '_'){
            $tmp = $tmp.'1';
        }
        ?>
        <a {{($curPage == 1 ? 'class=on' : 'href='. $tmp.'.html')}} >1</a>
        @if($index > 2) <a>...</a> @endif
        @for($f_index = 0; $f_index < $showBtn; $f_index++)
            @continue($index >= $lastPage)
            <a {{$curPage == $index ? 'class=on' : 'href=' . $href . $index . '.html'}}>{{$index}}</a>
            <?php $index++; ?>
        @endfor
        @if($index < $lastPage) <a>...</a> @endif
        <a {{$curPage == $lastPage ? 'class=on' : 'href=' . $href . $lastPage . '.html'}}>{{$lastPage}}</a>
        {{--<p><span>{{$curPage}}</span>/{{$lastPage}}</p>--}}
        @if ($lastPage > 7 && $curPage != $lastPage) <a class="down" href="{{$href}}{{$curPage + 1}}.html" >下一页</a> @endif
    </div>
@else
    <div class="page">
    <a class="on" href="{{$href}}1.html">1</a>
    </div>
@endif