<?php
$sportName = "";
$sport = $match['sport'];
$mid = $match['mid'];
$lid = $match['lid'];
$type = "";
if ($sport == 1) {
    $sportName = "足球";
    $type = "football";
} else if ($sport == 2) {
    $sportName = "篮球";
    $type = "basketball";
} else {
    $sportName = $match['project'];
    $type = "other";
}
$channels = $match['channels'];

$url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $mid);
$hteamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $match['hid']);
$ateamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $match['aid']);
?>
<div class="live_item">
    <p class="live_match_info">{{$match['league_name']}}<span>{{date('m-d H:i', strtotime($match['time']))}}</span></p>
    <div class="live_match_team">
        <p class="team"><span><a target="_blank" href="{{$hteamUrl}}">{{$match['hname']}}</a></span></p>
        @if($match['status'] > 0)
            <p class="vs"><span>直播中</span></p>
        @else
            <p class="vs">VS</p>
        @endif
        <p class="team"><span><a target="_blank" href="{{$ateamUrl}}">{{$match['aname']}}</a></span></p>
    </div>
    <div class="live_match_line">
        @if(isset($channels) && count($channels) > 0)
            @foreach($channels as $index=>$channel)
                @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                <a target="_blank" href="{{$channel['link']}}">{{$channel['name']}}</a>
                @else
                    <?php
                    if (isset($channel['akq_url']) && strlen($channel['akq_url']) > 0) {
                        $tmp_url = $channel['akq_url'];
                    } else {
                        $tmp_url = $url;
                    }
                    ?>
                    <a target="_blank" href="{{$tmp_url . '#btn=' . $index}}">{{$channel['name']}}</a>
                @endif
            @endforeach
        @endif
    </div>
</div>