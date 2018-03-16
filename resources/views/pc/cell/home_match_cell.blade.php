<?php if (!isset($match)) return ""; ?>
<tr>
    <td>
        @if($match['sport'] == 2) <p class="basketball">篮球</p> @else <p class="football">足球</p> @endif
    </td>
    <td>{{$match['league_name']}}</td>
    <td>{{date('H:i', strtotime($match['time']))}}</td>
    <td>{{$match['hname']}}</td>
    {{--<td><img class="icon" src="{{$match['host_icon']}}"></td>--}}
    <td></td>
    <td>VS</td>
    <td></td>
    {{--<td><img class="icon" src="{{$match['away_icon']}}"></td><!--球队默认icon-->--}}
    <td>{{$match['aname']}}</td>
    <td>
        <?php $channels = $match['channels'];?>
        @foreach($channels as $index=>$channel)
            <?php $impt = isset($channel['impt']) ? $channel['impt'] : 1; $impt_style = $impt == 2 ? 'style="color: red"' : '' ?>
            @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                <a {!! $impt_style !!} target="_blank" href="/live/ex-link/{{$channel['id']}}">{{$channel['name']}}</a>
            @else
                @if($match['sport'] == 2)
                    <a {!! $impt_style !!} target="_blank" href="{{str_replace('https://','http://',asset('/live/basketball/'.$match['mid'].'.html?btn='.$index))}}">{{$channel['name']}}</a>
                @else
                    <a {!! $impt_style !!} target="_blank" href="{{str_replace('https://','http://',asset('/live/football/'.$match['mid'].'.html?btn='.$index))}}">{{$channel['name']}}</a>
                @endif
            @endif
        @endforeach
        @if($match['sport'] == 1 && isset($match['has_article']) && $match['has_article']) <a style="color: red" target="_blank" href="https://liaogou168.com/match_detail/{{date('Ymd', strtotime($match['time']))}}/{{$match['mid']}}.html#Talent">专家推荐</a> @endif
    </td>
    <td>@if(isset($match['isMatching']) && $match['isMatching'])<p class="live">直播中</p> @endif</td>
</tr>