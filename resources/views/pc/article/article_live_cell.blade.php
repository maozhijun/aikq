    <dt>直播赛程</dt>
@foreach($matches as $match)
    <dd @if($match['status'] > 0) class="live" @endif >
        <?php
            $sportName = "";
            $sport = $match['sport'];
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
        ?>
        <p class="info">{{$sportName}}&nbsp;&nbsp;&nbsp;&nbsp;{{$match['league_name']}}&nbsp;&nbsp;{{date('m-d H:i', strtotime($match['time']))}}</p>
        <p class="match">{{$match['hname']}}&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}</p>
        @if(isset($channels) && count($channels) > 0)
        <p class="line">
            @foreach($channels as $index=>$channel)
                <a target="_blank" href="/live/{{$type}}/{{$match['mid']}}.html?btn={{$index}}">{{$channel['name']}}</a>
            @endforeach
        </p>
        @endif
    </dd>
@endforeach