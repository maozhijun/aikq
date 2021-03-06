@if(isset($match))
    <?php
    $isShowDate = true;
    $clazz = $match['sport'] == 2 ? "basketball" : "football";

    $isHomeLose = false; $isAwayLose = false;
//    if ($impt == 2) {
//        $clazz .= ' good';
//    }
    if ($match["status"] > 0){
        $clazz .= ' live';
    } elseif ($match["status"] == -1) {
        $clazz .= ' end';
        if ($match['hscore'] > $match['ascore']) {
            $isAwayLose = true;
        } else if ($match['hscore'] < $match['ascore']) {
            $isHomeLose = true;
        }
    }
    $url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'],$match['mid']);
    ?>
    <a href="{{$url}}" class="{{$clazz}}">
        <div class="team_con">
            @if($match["status"] == -1 || $match["status"] > 0)
                <p @if($isHomeLose)class="lose" @endif><span>{{$match['hscore']}}</span>{{$match['hname']}}</p>
                <p @if($isAwayLose)class="lose" @endif><span>{{$match['ascore']}}</span>{{$match['aname']}}</p>
            @else
                <p>{{$match['hname']}}</p>
                <p>{{$match['aname']}}</p>
            @endif
        </div>
        <div class="info_con">
            @if(isset($hideLeague) && $hideLeague)
                <p>{{date('m-d', is_numeric($match['time']) ? $match['time'] : strtotime($match['time']))}}</p>
            @else
                <p>{{$match['league']}}</p>
            @endif
            <p>{{date('H:i', is_numeric($match['time']) ? $match['time'] : strtotime($match['time']))}}</p>
        </div>
        <div class="status_con"></div>
    </a>
@endif