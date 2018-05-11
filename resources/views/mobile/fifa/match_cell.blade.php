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
<a class="item" href="#">
    <div class="part group">
        <p>{{date('m/d H:i',$match['time'])}}</p>
        @if($status > 0)
            <p class="live"><span class="minute">{{$matchTime}}</span></p>
        @elseif($status == -1)
            <p class="end"></p>
        @else
            <p><img src="img/icon_living_n.png"></p>
        @endif
    </div>
    <div class="part">
        <p class="team"><img src="{{$hicon}}">{{$match['hname']}}</p>
        <p class="team"><img src="{{$aicon}}">{{$match['aname']}}</p>
    </div>
    <div class="part">
        <p>{{$match['hscore']}}</p>
        <p>{{$match['ascore']}}</p>
    </div>
    <div class="part half">
        <p>{{$match['hscorehalf']}}</p>
        <p>{{$match['ascorehalf']}}</p>
    </div>
    <div class="part">
        <p>
            <span class="yellow">{{$match['h_yellow']?$match['h_yellow']:0}}</span><span class="red">{{$match['h_red']?$match['h_red']:0}}</span>
        </p>
        <p>
            <span class="yellow">{{$match['a_yellow']?$match['a_yellow']:0}}</span><span class="red">{{$match['a_red']?$match['a_red']:0}}</span>
        </p>
    </div>
</a>