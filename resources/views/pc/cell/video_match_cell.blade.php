<?php if (!isset($match)) return ""; ?>
<tr>
    <td>
        @if($match['sport'] == 2) <p class="basketball">篮球</p> @elseif($match['sport'] == 1) <p class="football">足球</p>
        @else <p class="football">{{isset($match['project']) ? $match['project'] : ''}}</p> @endif
    </td>
    <td>{{$match['lname']}}</td>
    <td>{{date('H:i', $match['time'])}}</td>
    @if(isset($match['type']) && $match['type'] == 1)
        <td colspan="5">{{$match['hname']}}</td>
    @else
        <td>{{$match['hname']}}</td>
        <td></td>
        <td>{{$match['hscore']}} - {{$match['ascore']}}
            {{--@if($match['hscore'] > $match['ascore'])<b>{{$match['hscore']}}</b> @else {{$match['hscore']}} @endif--}}
            {{-----}}
            {{--@if($match['ascore'] > $match['hscore'])<b>{{$match['ascore']}}</b> @else {{$match['ascore']}} @endif--}}
        </td>
        <td></td>
        <td>{{$match['aname']}}</td>
    @endif
    <td>
        <?php
            $channels = $match['channels'];
            $url = \App\Http\Controllers\PC\MatchTool::subjectLink($match['id'], 'video');
        ?>
        {{--@foreach($channels as $index=>$channel)--}}
            <?php //$impt = isset($channel['impt']) ? $channel['impt'] : 1; $impt_style = $impt == 2 ? 'style="color: red"' : '' ?>
            {{--@if(isset($channel['player']) && $channel['player'] == 16)--}}{{-- 外链 --}}
                {{--<a target="_blank" href="/live/ex-link/{{$channel['id']}}">{{$channel['title']}}</a>--}}
            {{--@else--}}
                {{--<a target="_blank" href="{{$url . '?cid=' . $channel['id']}}">{{$channel['title']}}</a>--}}
            {{--@endif--}}
        {{--@endforeach--}}
        <a target="_blank" href="{{$url}}">观看录像</a>
        {{--@if($match['sport'] == 1)--}}
            {{--<a style="color: red" target="_blank" href="https://liaogou168.com/match_detail/{{date('Ymd', $match['time'])}}/{{$match['mid']}}.html#Talent">专家推荐</a>--}}
        {{--@elseif($match['sport'] == 2)--}}
            {{--<a style="color: red" target="_blank" href="https://liaogou168.com/basket_detail/{{date('Ymd', $match['time'])}}/{{$match['mid']}}.html">专家推荐</a>--}}
        {{--@endif--}}
    </td>
</tr>