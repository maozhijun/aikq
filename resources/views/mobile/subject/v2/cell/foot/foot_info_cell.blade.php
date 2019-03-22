<?php
    $curSeasonName = isset($season) ? $season['name'] : "";
?>
<div id="Info_con">
    <div class="name_con">
        <img src="{{$sl['icon']}}">
        <h1>{{$sl['name']}}</h1>
        <h2>{{$sl['name_long']}}</h2>
        <select>
            @foreach($seasons as $index=>$seasonName)
                @if($index == 0)
                    <option @if($seasonName == $curSeasonName) selected @endif value="/{{$sl['name_en']}}/">{{$seasonName}}赛季</option>
                @else
                    <option @if($seasonName == $curSeasonName) selected @endif value="/{{$sl['name_en']}}/{{$seasonName}}/">{{$seasonName}}赛季</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="tab_con">
        @if(isset($hasKnockout) && $hasKnockout)
            <p class="tab_item on" forItem="knockout">淘汰赛</p>
            <p class="tab_item" forItem="rank">排名</p>
        @else
            <p class="tab_item on" forItem="rank">排名</p>
        @endif
        <p class="tab_item" forItem="match">赛程</p>
        <p class="tab_item" forItem="player">球员榜</p>
        <p class="tab_item" forItem="news">资讯</p>
        <p class="tab_item" forItem="video">视频</p>
    </div>
</div>