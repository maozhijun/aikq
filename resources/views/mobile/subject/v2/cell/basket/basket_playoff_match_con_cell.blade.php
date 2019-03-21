<?php
$count = (isset($item) && isset($item['info']) && isset($item['info']['count'])) ? $item['info']['count'] : 7;
$win_count = intval($count) / 2;
$isHomeLose = false; $isAwayLose = false;
if (isset($item) && isset($item['info'])) {
    if ($item['info']['hscore'] > $win_count) {
        $isAwayLose = true;
    } else if ($item['info']['ascore'] > $win_count) {
        $isHomeLose = true;
    }
}
?>
@if(isset($item) && isset($item['info']))
    <div class="match_con">
        <p @if($isHomeLose)class="lose" @endif><span>{{$item['info']['hscore']}}</span>{{!empty($item['info']['hname_short'])?$item['info']['hname_short']:$item['info']['hname']}}</p>
        <p @if($isAwayLose)class="lose" @endif><span>{{$item['info']['ascore']}}</span>{{!empty($item['info']['aname_short'])?$item['info']['aname_short']:$item['info']['aname']}}</p>
    </div>
@else
    <div class="match_con"></div>
@endif