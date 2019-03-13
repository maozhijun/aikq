<?php
    $count = (isset($item) && isset($item['info']) && isset($item['info']['count'])) ? $item['info']['count'] : 7;
    $win_count = intval($count) / 2;

    if(isset($item) && isset($item['info'])) {
        $hname = strlen($item['info']['hname_short']) > 0 ? $item['info']['hname_short'] : $item['info']['hname'];
        $aname = strlen($item['info']['aname_short']) > 0 ? $item['info']['aname_short'] : $item['info']['aname'];
    }
?>
@if(isset($item) && isset($item['info']))
    <p><b @if($item['info']['hscore'] > $win_count)class="win" @endif>{{$item['info']['hscore']}}</b><a href="javascript:location.href=GetTeamDetailUrl(2, '{{$lid}}', '{{$item['info']['hid']}}')">{{$hname}}</a></p>
    <p><b @if($item['info']['ascore'] > $win_count)class="win" @endif>{{$item['info']['ascore']}}</b><a href="javascript:location.href=GetTeamDetailUrl(2, '{{$lid}}', '{{$item['info']['aid']}}')">{{$aname}}</a></p>
@else
    <p><b></b><a href="javascript:void(0)"></a></p>
    <p><b></b><a href="javascript:void(0)"></a></p>
@endif