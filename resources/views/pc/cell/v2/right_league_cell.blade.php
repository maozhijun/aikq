<?php
$leagueName = isset($zhuanti) ? $zhuanti['name'] : "其他";
$leagueNameLong = isset($zhuanti) ? $zhuanti['name_long'] : "其他";
$leaguePath = isset($zhuanti) ? '/'.$zhuanti['name_en'].'/' : "/";
$leagueIcon = isset($zhuanti) ? $zhuanti['icon'] : "";
$leagueTeamCount= isset($zhuanti) ? $zhuanti['team_count'] : 0;
?>
<a class="banner_entra" href="{{$leaguePath}}">
    <img src="{{$leagueIcon}}">
    <h3>{{$leagueNameLong}}</h3>
    <p>球队：<span>{{$leagueTeamCount}}支</span></p>
</a>