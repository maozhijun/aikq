<?php if (!isset($match)) return ""; ?>
<tr>
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
        <?php
            $channels = $match['channels'];
            $sport = $match['sport'];
            $mid = $match['mid'];
            $link = 'https:'.env('WWW_URL').'/live/spPlayer/player-' . $mid . '-' . $sport . '.html';
        ?>
        <input type="text" name="link" id="ch_{{$mid}}_{{$sport}}" value="{{$link}}"><button class="copy" for="ch_{{$mid}}_{{$sport}}">复制</button>
    </td>
    <td>@if(isset($match['isMatching']) && $match['isMatching'])<p class="live">直播中</p> @endif</td>
</tr>