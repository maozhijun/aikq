<?php if (!isset($match)) return ""; ?>
<?php
$channels = $match['channels'];
$sport = $match['sport'];
$mid = $match['mid'];
$lid = isset($match['lid']) ? $match['lid'] : 0;
$url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $mid);

$firstChannel = isset($channels[0]) ? $channels[0] : [];
$impt = isset($firstChannel['impt']) ? $firstChannel['impt'] : 1;
$impt_style = '';
if ($impt == 2) {
    if ($sport == 1) {
        $impt_style = 'football';
    } else if ($sport == 2) {
        $impt_style = 'basketball';
    }
}
$type = '';
if ($sport == 1) {
    $type = 'foot';
} else if ($sport == 2) {
    $type = 'basket';
}
//等级类型
if ($match['sport'] == 1){
    if (isset($match['genre'])){
        //是否是一级赛事
        $isFirst = ($match['genre'] >> 1 & 1) == 1;
    }
    else{
        $isFirst = 0;
    }
}
else if($match['sport'] == 2){
    $isFirst = $match['league_name'] == 'NBA' || $match['league_name'] == 'CBA';
}
else{
    $isFirst = 0;
}
//是否是竞彩
$isLottery = isset($match['betting_num']);
?>

<tr type="{{$type.'ball'}}">
    <td><img class="icon" src="{{env('CDN_URL')}}/img/pc/v2/icon_{{$type}}_light_opaque.png"></td>
    <td>{{$match['league_name']}}</td>
    <td>{{date('H:i', strtotime($match['time']))}}</td>
    @if(isset($match['hid']))
        <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
    @else
        <td>{{$match['hname']}}</td>
    @endif
    @if(isset($match['isMatching']) && $match['isMatching'])
        <td><p class="live">直播中</p></td>
    @else
        <td>vs</td>
    @endif
    @if(isset($match['aid']))
        <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['hname']}}</a></td>
    @else
        <td>{{$match['aname']}}</td>
    @endif
    <td class="channel">
        @foreach($channels as $channel)
            @continue($channel['platform'] == 5){{-- APP链接过滤 --}}
            @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
            <a target="_blank" rel="nofollow" href="{{$channel['link']}}">{{$channel['name']}}</a>
            @else
                <?php
                if(isset($channel['akq_url']) && strlen($channel['akq_url']) > 0){
                    $tmp_url = $channel['akq_url'];
                }
                else{
                    $tmp_url = $url;
                }
                ?>
                <a target="_blank" href="{{$tmp_url}}">{{$channel['name']}}</a>
            @endif
        @endforeach
    </td>
    <td>
        @if(strlen($impt_style) > 0)
            <img class="major" src="{{env('CDN_URL')}}/img/pc/v2/icon_import_n.png">
        @endif
    </td>
</tr>