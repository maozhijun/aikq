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
            $impt_style = 'class=good_ft';
        } else if ($sport == 2) {
            $impt_style = 'class=good_bk';
        }
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
<tr match="1" lottery="{{$isLottery}}" first="{{$isFirst}}" imp="{{$impt}}" {!! $impt_style !!}>
    <td>
        @if($match['sport'] == 2) <p class="basketball">篮球</p> @elseif($match['sport'] == 1) <p class="football">足球</p>
        @else <p class="football">{{isset($match['project']) ? $match['project'] : ''}}</p> @endif
    </td>
    <td>{{$match['league_name']}}</td>
    <td>{{date('H:i', strtotime($match['time']))}}</td>
    @if(isset($match['type']) && $match['type'] == 1)
        <td colspan="5">{{$match['hname']}}</td>
    @else
        @if(isset($match['hid']))
            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
        @else
            <td>{{$match['hname']}}</td>
        @endif
        <td></td>
        <td>VS</td>
        <td></td>
        @if(isset($match['aid']))
            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
        @else
            <td>{{$match['aname']}}</td>
        @endif
    @endif
    <td>
        @foreach($channels as $index=>$channel)
            @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                <a target="_blank" href="/live/ex-link/{{$channel['id']}}">{{$channel['name']}}</a>
            @else
                <?php
                if(isset($channel['akq_url']) && strlen($channel['akq_url']) > 0){
                    $tmp_url = $channel['akq_url'];
                }
                else{
                    $tmp_url = $url;
                }
                ?>
                <a target="_blank" href="{{$tmp_url . '?btn=' . $index}}">{{$channel['name']}}</a>
            @endif
        @endforeach
        {{--@if($match['sport'] == 1)--}}
            {{--<a style="color: red" target="_blank" href="https://liaogou168.com/match_detail/{{date('Ymd', strtotime($match['time']))}}/{{$match['mid']}}.html#Article">专家推荐</a>--}}
        {{--@elseif($match['sport'] == 2)--}}
            {{--<a style="color: red" target="_blank" href="https://liaogou168.com/basket_detail/{{date('Ymd', strtotime($match['time']))}}/{{$match['mid']}}.html">专家推荐</a>--}}
        {{--@endif--}}
    </td>
    <td>@if(isset($match['isMatching']) && $match['isMatching'])<p class="live">直播中</p> @endif</td>
</tr>