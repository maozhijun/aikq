<?php
    $count = isset($item) && isset($item['count']) ? $item['count'] : 7;
    $win_count = intval($count) / 2;
//    dump($item);
?>
@if(isset($item) && isset($item['info']))
    <p><b @if($item['info']['hscore'] > $win_count)class="win" @endif>{{$item['info']['hscore']}}</b><a href="javascript:location.href=GetTeamDetailUrl(2, '{{$lid}}', '{{$item['info']['hid']}}')">{{$item['info']['hname_short']}}</a></p>
    <p><b @if($item['info']['ascore'] > $win_count)class="win" @endif>{{$item['info']['ascore']}}</b><a href="javascript:location.href=GetTeamDetailUrl(2, '{{$lid}}', '{{$item['info']['aid']}}')">{{$item['info']['aname_short']}}</a></p>
@else
    <p><b>-</b><a href="javascript:void(0)">-</a></p>
    <p><b>-</b><a href="javascript:void(0)">-</a></p>
@endif