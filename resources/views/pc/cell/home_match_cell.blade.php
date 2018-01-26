<?php if (!isset($match)) return ""; ?>
<tr>
    <td>
        @if($match['sport'] == 2) <p class="basketball">篮球</p> @else <p class="football">足球</p> @endif
    </td>
    <td>{{$match['league_name']}}</td>
    <td>{{date('H:i', strtotime($match['time']))}}</td>
    <td>@if(isset($match['isMatching']) && $match['isMatching'])<img src="/img/pc/icon_live.png"> @endif</td>
    <td>{{$match['hname']}}</td>
    <td><img class="icon" src="{{$match['host_icon']}}"></td>
    <td></td>
    <td>VS</td>
    <td></td>
    <td><img class="icon" src="{{$match['away_icon']}}"></td><!--球队默认icon-->
    <td>{{$match['aname']}}</td>
    <td>
        <?php $channels = $match['channels'];?>
        @foreach($channels as $index=>$channel)
            @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                <a target="_blank" href="/live/ex-link/{{$channel['id']}}">{{$channel['name']}}</a>
            @else
                @if($match['sport'] == 2)
                    <a target="_blank" href="{{str_replace('https://','http://',asset('/live/basketball/'.$match['mid'].'.html?btn='.$index))}}">{{$channel['name']}}</a>
                @else
                    <a target="_blank" href="{{str_replace('https://','http://',asset('/live/football/'.$match['mid'].'.html?btn='.$index))}}">{{$channel['name']}}</a>
                @endif
            @endif
        @endforeach
    </td>
</tr>