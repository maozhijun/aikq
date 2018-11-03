    <dt>直播赛程</dt>
@foreach($matches as $match)
    <dd @if($match['status'] > 0) class="live" @endif >
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
        ?>
        <p class="info">{{$sportName}}&nbsp;&nbsp;&nbsp;&nbsp;{{$match['league_name']}}&nbsp;&nbsp;{{date('m-d H:i', strtotime($match['time']))}}</p>
        <p class="match"><a target="_blank" href="{{$url}}">{{$match['hname']}}&nbsp;VS&nbsp;{{$match['aname']}}</a></p>
        @if(isset($channels) && count($channels) > 0)
        <p class="line">
            @foreach($channels as $index=>$channel)
                {{--<a target="_blank" href="/live/{{$type}}/{{$match['mid']}}.html#btn={{$index}}">{{$channel['name']}}</a>--}}

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
                    <a target="_blank" href="{{$tmp_url . '#btn=' . $index}}">{{$channel['name']}}</a>
                @endif
            @endforeach
        </p>
        @endif
    </dd>
@endforeach