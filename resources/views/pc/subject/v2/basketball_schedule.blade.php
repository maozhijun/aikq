<?php
    $dateArray = [];
    foreach($scheduleMatches as $date=>$matchArray) {
        $dateArray[] = $date;
    }
?>
<div class="el_con">
    <div class="header">
        <h3><p>{{$sl["name"]}}赛程</p></h3>
        <div class="date">
            <button class="left">前三天</button>
            <p class="con_text">{{substr($dateArray[0], 5, 5)}}至{{substr($dateArray[count($dateArray) - 1], 5, 5)}}</p>
            <button class="right">后三天</button>
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
            $mid = $match["mid"];
            $status = $match["status"];
            $time = date('Y-m-d H:i', $match["time"]);
            $statusCn = \App\Models\Match\BasketMatch::getStatusTextCn($status);
            $hTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $sl["lid"], $match["hid"]);
            $aTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $sl["lid"], $match["aid"]);
            $detailUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sl["sport"], $sl["lid"], $mid);
            $matchLive = \App\Models\Match\MatchLive::getMatchLiveByMid($sport, $mid);
            $channels = isset($matchLive) ? $matchLive->kAiKqChannels() : null;
            $living = isset($channels) && ($status > 0 || (strtotime($time) + 20 * 60 > time()) );
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
                @if(isset($channels))
                    <?php $btnIndex = 0; ?>
                    @foreach($channels as $channel)
                        @continue($channel["platform"] == 5) {{-- 过滤app --}}
                        @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                            <a class="live" target="_blank" href="{{$channel['link']}}">{{$channel['name']}}</a>
                        @else
                            <a class="live" target="_blank" href="{{$detailUrl . '#btn=' . ($btnIndex++)}}">{{$channel['name']}}</a>
                        @endif
                    @endforeach
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endforeach
</div>
</div>