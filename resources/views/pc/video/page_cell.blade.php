@if(!isset($page) || $page["lastPage"] == 1)
    <div class="page"><a href="#" class="on">1</a></div>
@else
<?php
    $showPage = 7; $currPage = $page["curPage"]; $lastPage = $page["lastPage"];
    $startPage = $currPage - intval($showPage / 2);
    if ($startPage < 2) {
        $startPage = 2;
    } else if ($startPage >= $lastPage - $showPage) {
        $startPage = $lastPage - $showPage;
    }
?>
    <div class="page">
        <a @if($currPage == 1) class="on" href="#" @else href="{{str_replace("page", 1, $pageUrl)}}" @endif >1</a>
        @if($startPage > 2) <p>...</p> @endif
        @for($index = 0;$index < $showPage; $index++)
            @continue($startPage + $index >= $lastPage)
            <a @if($startPage + $index == $currPage) class="on" href="#" @else href="{{str_replace("page", $startPage + $index, $pageUrl)}}" @endif >{{$startPage + $index}}</a>
        @endfor
        @if($startPage < $lastPage - $showPage) <p>...</p> @endif
        <a @if($currPage == $lastPage) class="on" href="#" @else href="{{str_replace("page", $lastPage, $pageUrl)}}" @endif >{{$lastPage}}</a>
    </div>
@endif