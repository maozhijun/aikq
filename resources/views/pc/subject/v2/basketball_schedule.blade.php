<div class="el_con">
    <div class="header">
        <h3><p>{{$sl["name"]}}赛程</p></h3>
        <div class="date">
            <button class="left" name="{{$sl["name_en"]}}" season="{{$season["name"]}}" time="{{$start}}">前三天</button>
            <p class="con_text">{{date('m-d', $start)}}至{{date('m-d', $end)}}</p>
            <button class="right" name="{{$sl["name_en"]}}" season="{{$season["name"]}}" time="{{$end + 60}}">后三天</button>
        </div>
    </div>
    <div class="schedule_con">
    @foreach($scheduleMatches as $date=>$matchArray)
    <p class="date_text">{{date('m月d日', strtotime($date))}}@if($date == date('Y-m-d'))（今天）@endif</p>
    <table class="match">
        <col width="11%"><col><col width="12%"><col><col width="42.5%">
        @foreach($matchArray as $match)
        <?php
            $sport = $sl["sport"];
            $lid = $sl["lid"];
            $mid = $match["mid"];
            $status = $match["status"];
            $time = $match["time"];//date('Y-m-d H:i', $match["time"]);
            $hid = $match["hid"];
            $aid = $match["aid"];
            $name_en = $sl["name_en"];
            $statusCn = \App\Models\Match\BasketMatch::getStatusTextCn($status);
            $hTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $hid);
            $aTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $aid);
            $detailUrl = "/".$name_en."/live".$sport.\App\Http\Controllers\PC\CommonTool::getMatchVsByTid($hid, $aid).".html";
            $living = ($status > 0 || (time() + 20 * 60 > strtotime($time) && $status == 0) );
        ?>
        <tr>
            <td>{{substr($time, 11, 5)}}</td>
            <td class="host"><a target="_blank" href="{{$hTeamUrl}}">{{$match["hname"]}}</a></td>
            <td class="vs">
                @if($living)
                    <span class="living">直播中</span>
                @elseif($status == -1 || $status > 0)
                    {{$statusCn}}
                @elseif($status == 0)
                    vs
                @endif
            </td>
            <td class="away"><a target="_blank" href="{{$aTeamUrl}}">{{$match["aname"]}}</a></td>
            <td class="line">
                <a class="live" target="_blank" href="{{$detailUrl}}">观看直播</a>
            </td>
        </tr>
        @endforeach
    </table>
    @endforeach
</div>
</div>