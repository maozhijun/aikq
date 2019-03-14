<table round="{{$round}}" class="match" @if($status == 0) style="display: none;" @endif >
    <colgroup><col width="25%"><col><col width="12%"><col><col width="20%"></colgroup>
    <tbody>
    @foreach($schMatches as $match)
        <?php
        $status = $match["status"]; $sport = $sl["sport"]; $lid = $sl["lid"]; $mid = $match["mid"];
        $time = date('Y-m-d H:i', $match["time"]); $hid = $match["hid"]; $aid = $match["aid"];
        $name_en = $sl["name_en"];
        $hTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $sport, $hid);
        $aTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $sport, $aid);
        $liveUrl = "/".$sl["name_en"]."/live".$sport.\App\Http\Controllers\PC\CommonTool::getMatchVsByTid($hid, $aid).".html";
        ?>
        <tr>
            <td><span>{{$time}}</span></td>
            <td class="host"><a target="_blank" href="{{$hTeamUrl}}">{{$match["hname"]}}</a></td>
            <td class="vs">
                @if($status > 0) <span class="living">直播中</span>
                @elseif($status == -1 || $status > 0) {{$match["hscore"] . " - " . $match["ascore"]}}
                @else vs
                @endif
            </td>
            <td class="away"><a target="_blank" href="{{$aTeamUrl}}">{{$match["aname"]}}</a></td>
            <td class="line">
                <a target="_blank" href="{{$liveUrl}}" class="live">观看直播</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>