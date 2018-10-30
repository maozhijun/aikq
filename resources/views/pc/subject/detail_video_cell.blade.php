<?php
    $hasRound = false;//!empty($match['round']);
    foreach ($lives as $day=>$matches) {
        foreach ($matches as $m) {
            $hasRound = !empty($m['round']);
        }
        break;
    }
?>
<table id="Video" class="live" style="display: none;">
    <colgroup>
        <col num="1" width="{{$hasRound ? '10%' : '0'}}">
        <col num="2" width="8%">
        <col num="3" width="0">
        <col num="4" width="20%">
        <col num="5" width="20%">
        <col num="6" width="20%">
        <col num="7" width="23%">
    </colgroup>
    <tbody>
    @foreach($lives as $day=>$matches)
        <tr>
            <th colspan="7">{{date('Y年m月d日', $day)}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</th>
        </tr>
        @foreach($matches as $match)
        <?php $firstCh = isset($match['channels'][0]) ? $match['channels'][0] : null; ?>
        <tr>
            <td>
                @if($hasRound)
                    @if(isset($subject['type']) && $subject['type'] == 2)
                        <span>{{$match['round']}}</span>
                    @else
                        <span>第{{$match['round']}}轮 </span>
                    @endif
                @endif
            </td>
            <td>{{date('H:i', $match['time'])}}</td>
            <td></td>
            <td>{{$match['hname']}}</td>
            <td>{{$match['hscore'] . ' - ' . $match['ascore']}}</td>
            <td>{{$match['aname']}}</td>
            <td>
                @if(isset($firstCh))
                <a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($slid, $firstCh['id'], 'video')}}">观看录像</a>
                @endif
            </td>
        </tr>
        @endforeach
    @endforeach
    {{--<tr>--}}
        {{--<th colspan="7"><a href="/live/subject/videos/{{$slid}}/1.html" style="color:#4492fd;">查看更多录像</a></th>--}}
    {{--</tr>--}}
    </tbody>
</table>