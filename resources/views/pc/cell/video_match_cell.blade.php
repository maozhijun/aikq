<?php if (!isset($match)) return ""; ?>
<tr>
    <td>
        @if($match['sport'] == 2) <p class="basketball">篮球</p> @elseif($match['sport'] == 1) <p class="football">足球</p>
        @else <p class="football">{{isset($match['project']) ? $match['project'] : ''}}</p> @endif
    </td>
    <td>{{$match['lname']}}</td>
    <td>{{date('H:i', $match['time'])}}</td>
    @if(isset($match['type']) && $match['type'] == 1)
        <td colspan="5">{{$match['hname']}}</td>
    @else
        <td>{{$match['hname']}}</td>
        <td></td>
        <td>{{$match['hscore']}} - {{$match['ascore']}}</td>
        <td></td>
        <td>{{$match['aname']}}</td>
    @endif
    <td>
        <?php $channels = $match['channels']; $url = \App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($match['s_lid'], $match['id'], 'video'); ?>
        <a target="_blank" href="{{$url}}">观看录像</a>
    </td>
</tr>