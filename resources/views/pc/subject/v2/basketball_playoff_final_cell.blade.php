<?php $cdnUrl = env("CDN_URL"); ?>
@if(isset($item))
    <div class="finals_match">
        <img src="{{$cdnUrl}}/img/pc/v2/image_basketball_n.png" class="cup">
        @if(isset($item['info']))
            <?php
            //东部在左，西部在右，需要调节一下顺序
            $hicon = isset($item['info']['hicon']) ? $item['info']['hicon'] : "";
            $aicon = isset($item['info']['aicon']) ? $item['info']['aicon'] : "";
            $hid = $item['info']['hid'];
            $aid = $item['info']['aid'];
            $hname = $item['info']['hname_short'];
            $aname = $item['info']['aname_short'];
            $hscore = $item['info']['hscore'];
            $ascore = $item['info']['ascore'];
            if ($item['info']['hzone'] == 0) {
                $tempIcon = $hicon; $hicon = $aicon; $aicon = $tempIcon;
                $tempName = $hname; $hname = $aname; $aname = $tempName;
                $tempScore = $hscore; $hscore = $ascore; $ascore = $tempScore;
                $tempId = $hid; $hid = $aid; $aid = $tempId;
            }
            ?>
            <div class="team_con">
                <p class="team"><img src="{{$hicon}}"><span>{{$hname}}</span></p>
                <p class="score">{{$hscore}}&nbsp;&nbsp;&nbsp;{{$ascore}}</p>
                <p class="team"><img src="{{$aicon}}"><span>{{$aname}}</span></p>
            </div>
        @endif
        <ul>
            @foreach($item['matches'] as $match)
                <li>
                    <a href="{{\App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $match['mid'])}}">
                        @if($match['hscore'] > $match['ascore'])
                            <p class="icon"><img src="{{$cdnUrl}}/img/pc/v2/image_basketball_n.png"></p>
                        @else
                            <p class="icon"></p>
                        @endif
                        <p class="host">{{$match['hid'] == $hid ? $hname : $aname}}</p>
                        <p class="score">{{$match['hscore']}}</p>
                        <p class="vs">-</p>
                        <p class="score">{{$match['ascore']}}</p>
                        <p class="away">{{$match['aid'] == $aid ? $aname : $hname}}</p>
                        @if($match['hscore'] < $match['ascore'])
                            <p class="icon"><img src="{{$cdnUrl}}/img/pc/v2/image_basketball_n.png"></p>
                        @else
                            <p class="icon"></p>
                        @endif
                    </a>
                </li>
                {{--<li><p>-</p></li>--}}
            @endforeach
        </ul>
    </div>
@else
    <div class="finals_match">
        <img src="{{$cdnUrl}}/img/pc/v2/image_basketball_n.png" class="cup">
    </div>
@endif