@extends("pc.layout.v2.base")
<?php $cdnUrl = env("CDN_URL"); ?>
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/left_right_2.css?201903221050">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/league_nba_2.css">
@endsection
@section("content")
<div id="Crumbs">
    <div class="def_content">
        <a href="/">爱看球</a> - {{$sl["name"]}}
    </div>
</div>
<div class="def_content" id="Part_parent">
    <div id="Left_part">
        @if(isset($playoff) && count($playoff) > 0)
            @include('pc.subject.v2.basketball_playoff_cell', ['playoff'=>$playoff, 'lid'=>$sl['lid']])
        @endif
        @include("pc.subject.v2.basketball_schedule", ["scheduleMatches"=>$scheduleMatches, "sl"=>$sl, "start"=>$start, "end"=>$end, "season"=>$season])
        <div class="el_con">
            <div class="header">
                <h3><p>{{$sl["name"]}}排名</p></h3>
            </div>
            <div class="rank_con">
                @if(isset($eastRanks) && count($eastRanks) > 0)
                <p class="date_text">东部</p>
                <table class="rank">
                    <col width="7.8%"><col><col width="7%"><col width="7%"><col width="9.6%"><col width="7%"><col width="7%"><col width="7%"><col width="7%"><col width="7%"><col width="7%"><col width="9.6%">
                    <tr>
                        <th>排名</th>
                        <th class="team">球队</th>
                        <th>胜</th>
                        <th>负</th>
                        <th>胜场差</th>
                        <th>胜率</th>
                        <th>主场</th>
                        <th>客场</th>
                        <th>得分</th>
                        <th>失分</th>
                        <th>净胜</th>
                        <th>连胜/负</th>
                    </tr>
                    @foreach($eastRanks as $rank)
                    <?php
                        $teamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($sl["name_en"], $sl["sport"], $rank["tid"]);
                        $win = $rank["win"];
                        $lose = $rank["lose"];
                        $total = $win + $lose;
                        $win_p = $total > 0 ? round($win / $total, 2) * 100 : 0;
                    ?>
                        <tr>
                            <td>{{$rank["rank"]}}</td>
                            <td class="team"><a href="{{$teamUrl}}"><img src="{{$rank["ticon"]}}">{{$rank["tname"]}}</a></td>
                            <td>{{$rank["win"]}}</td>
                            <td>{{$rank["lose"]}}</td>
                            <td>{{$rank["win_diff"]}}</td>
                            <td>{{$win_p}}%</td>
                            <td>{{$rank["home_bat_w"]}}-{{$rank["home_bat_l"]}}</td>
                            <td>{{$rank["away_bat_w"]}}-{{$rank["away_bat_l"]}}</td>
                            <td>{{$rank["goal"]}}</td>
                            <td>{{$rank["fumble"]}}</td>
                            <td>{{number_format($rank["goal"] - $rank["fumble"], 1)}}</td>
                            <td>{{abs($rank["win_status"]) . ($rank["win_status"] < 0 ? "连负" : "连胜")}}</td>
                        </tr>
                    @endforeach
                </table>
                @endif
                <p class="date_text">@if($sl["name"] == "NBA")西部@endif</p>
                <table class="rank">
                    <col width="7.8%"><col><col width="7%"><col width="7%"><col width="9.6%"><col width="7%"><col width="7%"><col width="7%"><col width="7%"><col width="7%"><col width="7%"><col width="9.6%">
                    <tr>
                        <th>排名</th>
                        <th class="team">球队</th>
                        <th>胜</th>
                        <th>负</th>
                        <th>胜场差</th>
                        <th>胜率</th>
                        <th>主场</th>
                        <th>客场</th>
                        <th>得分</th>
                        <th>失分</th>
                        <th>净胜</th>
                        <th>连胜/负</th>
                    </tr>
                    @foreach($westRanks as $rank)
                    <?php
                        $teamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($sl["name_en"], $sl["sport"], $rank["tid"]);
                        $win = $rank["win"];
                        $lose = $rank["lose"];
                        $total = $win + $lose;
                        $win_p = $total > 0 ? round($win / $total, 2) * 100 : 0;
                    ?>
                    <tr>
                        <td>{{$rank["rank"]}}</td>
                        <td class="team"><a href="{{$teamUrl}}"><img src="{{$rank["ticon"]}}">{{$rank["tname"]}}</a></td>
                        <td>{{$rank["win"]}}</td>
                        <td>{{$rank["lose"]}}</td>
                        <td>{{$rank["win_diff"]}}</td>
                        <td>{{$win_p}}%</td>
                        <td>{{$rank["home_bat_w"]}}-{{$rank["home_bat_l"]}}</td>
                        <td>{{$rank["away_bat_w"]}}-{{$rank["away_bat_l"]}}</td>
                        <td>{{$rank["goal"]}}</td>
                        <td>{{$rank["fumble"]}}</td>
                        <td>{{number_format($rank["goal"] - $rank["fumble"], 1)}}</td>
                        <td>{{abs($rank["win_status"]) . ($rank["win_status"] < 0 ? "连负" : "连胜")}}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    @include("pc.subject.v2.right_part_cell", ["sl"=>$sl, "articles"=>isset($comboData["articles"]) ? $comboData["articles"] : array(), "videos"=>isset($comboData["videos"])?$comboData["videos"]:array(), "season"=>$season, "data"=>$data, "seasons"=>$seasons])
</div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env("CDN_URL")}}/js/pc/v2/league_nba_2.js?time=201903181052"></script>
    <script type="text/javascript">
        var LeagueKeyword = '{{$sl["name_en"]}}';
        var NowDate = '{{date('Y-m-d')}}';//'2019-02-21';
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection