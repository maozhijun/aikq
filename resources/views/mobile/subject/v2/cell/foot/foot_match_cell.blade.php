<?php
    $curRound = isset($curRound) ? $curRound : 1;
?>
<div class="match_con match" style="display: none;">
    @if(isset($schedules) && count($schedules) > 0)
        @foreach($schedules as $key=>$matches)
            @if(isset($key) && is_numeric($key))
                <p class="match_list_date @if($key==$curRound)on @endif">第{{$key}}轮</p>
            @endif
            <div class="match_list_con">
                @foreach($matches as $match)
                    @include('mobile.cell.v2.match_item_cell', ['match'=>$match, 'hideLeague'=>true])
                @endforeach
            </div>
        @endforeach
    @endif
</div>