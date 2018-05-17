<?php
$status = $match['status'];
$mid = $match['mid'];
$lid = $match['lid'];

if($status > 0){
    $matchTime = \App\Http\Controllers\PC\CommonTool::getMatchWapCurrentTime($match['time'],$match['timehalf'],$match['status']);;
}
else{
    $matchTime = '';
}

$matchUrl = \App\Http\Controllers\PC\CommonTool::matchWapPathWithId($mid,1);

if($status > 0)
    $matchUrl = $matchUrl.'#Match';

//角球比分
$cornerScore = "-";
//半场比分
$halfScore = "";
if ($status > 0 || $status == -1) {
    $halfScore = ($status == 1) ? "" : ('（'.$match['hscorehalf'] . " - " . $match['ascorehalf'].'）');

    if (isset($match['h_corner'])) {
        $cornerScore = $match['h_corner'] . " - " . $match['a_corner'];
    }
}

//默认是否显示
$liveUrl = \App\Http\Controllers\PC\CommonTool::matchWapLivePathWithId($match['mid']);

$hicon = isset($match['hicon']) && strlen($match['hicon']) > 0 ? $match['hicon'] : '/phone/img/icon_teamDefault.png';
$aicon = isset($match['aicon']) && strlen($match['aicon']) > 0 ? $match['aicon'] : '/phone/img/icon_teamDefault.png';
?>
<tr @if($status == -1)class="end"@endif>
    <td>{{date('m.d',$match['time'])}}<br/>{{date('H:i',$match['time'])}}</td>
    <td>{{$match['hname']}}</td>
    @if($status > 0 || $status == -1)
        <td>{{$match['hscore']}} - {{$match['ascore']}}</td>
    @else
        <td>VS/td>
    @endif
    <td>{{$match['aname']}}</td>
    @if($status > 0)
        <td><p class="live">比赛中</p></td>
    @elseif($status == 0)
        <td><img src="img/icon_living_n.png"></td>
    @elseif($status == -1)
        <p class="end">已结束</p>
    @else
        <p class="end">异常</p>
    @endif
    <td><a href="">析</a><a href="">大</a><a href="">小</a><a href="">欧</a><a href="">荐</a></td>
    <td>
        @if($status == -1)
            <a href="">精彩回放</a>
        @else
            <a href="">高清直播</a><a href="">CCTV5</a>
        @endif
    </td>
</tr>