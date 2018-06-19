<?php if (!isset($match)) return ""; ?>
<?php
    $channels = $match['channels'];
    $sport = $match['sport'];
    $mid = $match['mid'];
    if ($sport == 3) {
        //$url = str_replace('https://','http://',asset('/live/other/'. $mid .'.html'));
        $url = '/live/other/'. $mid .'.html';
    } else if ($sport == 2) {
        //$url = str_replace('https://','http://',asset('/live/basketball/'. $mid .'.html'));
        $url = '/live/basketball/'. $mid .'.html';
    } else {
        //$url = str_replace('https://','http://',asset('/live/football/'. $mid .'.html'));
        $url = '/live/football/'. $mid .'.html';
    }

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
?>
<tr {!! $impt_style !!}>
    <td>
        @if($match['sport'] == 2) <p class="basketball">篮球</p> @elseif($match['sport'] == 1) <p class="football">足球</p>
        @else <p class="football">{{isset($match['project']) ? $match['project'] : ''}}</p> @endif
    </td>
    <td>{{$match['league_name']}}</td>
    <td>{{date('H:i', strtotime($match['time']))}}</td>
    @if(isset($match['type']) && $match['type'] == 1)
        <td colspan="5">{{$match['hname']}}</td>
    @else
        <td>{{$match['hname']}}</td>
        <td></td>
        <td>VS</td>
        <td></td>
        <td>{{$match['aname']}}</td>
    @endif
    <td>
        @foreach($channels as $index=>$channel)
            @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                <a target="_blank" href="/live/ex-link/{{$channel['id']}}">{{$channel['name']}}</a>
            @else
                <a target="_blank" href="{{$url . '?btn=' . $index}}">{{$channel['name']}}</a>
            @endif
        @endforeach
        @if($match['sport'] == 1)
            <a style="color: red" target="_blank" href="https://liaogou168.com/match_detail/{{date('Ymd', strtotime($match['time']))}}/{{$match['mid']}}.html#Article">专家推荐</a>
        @elseif($match['sport'] == 2)
            <a style="color: red" target="_blank" href="https://liaogou168.com/basket_detail/{{date('Ymd', strtotime($match['time']))}}/{{$match['mid']}}.html">专家推荐</a>
        @endif
    </td>
    <td>@if(isset($match['isMatching']) && $match['isMatching'])<p class="live">直播中</p> @endif</td>
</tr>