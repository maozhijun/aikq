@extends("pc.layout.v2.base")
<?php $cdnUrl = env("CDN_URL"); ?>
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/left_right_2.css">
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
        @if(isset($season["kind"]) && $season["kind"] ==  2)
        <div class="knockout_con basketball">
            <div class="round_con" style="display: ">
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html"><a href="team.html">凯尔特人</a></a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="line_left_con" style="height: 41px; top: 32px; left: 10px; border-bottom: none;"></div>
            </div>
            <div class="round_con" style="display: none;">
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">开拓者</a></p>
                    <p><b>0</b><a href="team.html">灰熊</a></p>
                </div>
                <div class="match_con" style="margin-top: 30px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">开拓者</a></p>
                    <p><b>0</b><a href="team.html">灰熊</a></p>
                </div>
                <div class="line_left_con" style="height: 82px; top: 32px; left: 10px;"></div>
                <div class="line_left_con" style="height: 82px; top: 210px; left: 10px;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
            </div>
            <div class="round_con" style="display: none;">
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">开拓者</a></p>
                    <p><b>0</b><a href="team.html">灰熊</a></p>
                </div>
                <div class="match_con" style="margin-top: 30px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">开拓者</a></p>
                    <p><b>0</b><a href="team.html">灰熊</a></p>
                </div>
                <div class="line_right_con" style="height: 82px; top: 32px; right: 10px;"></div>
                <div class="line_right_con" style="height: 82px; top: 210px; right: 10px;"></div>
            </div>
            <div class="round_con" style="display: ">
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="line_right_con" style="height: 41px; top: 32px; right: 10px; border-bottom: none;"></div>
            </div>
            <div class="finals_match">
                <img src="img/image_basketball_n.png" class="cup">
                <div class="team_con">
                    <p class="team"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/15.png"><span>湖人</span></p>
                    <p class="score">3&nbsp;&nbsp;&nbsp;2</p>
                    <p class="team"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/15.png"><span>凯尔特人</span></p>
                </div>
                <ul>
                    <li>
                        <a href="live.html">
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                            <p class="host">湖人</p>
                            <p class="score">104</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"></p>
                        </a>
                    </li>
                    <li><p>-</p></li>
                    <li><p>-</p></li>
                </ul>
            </div>
        </div>
        @endif
        @component("pc.subject.v2.basketball_schedule", ["scheduleMatches"=>$scheduleMatches, "sl"=>$sl]) @endcomponent
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
                        <tr>
                            <td>{{$rank["rank"]}}</td>
                            <td class="team"><a href="team.html"><img src="{{$rank["icon"]}}">{{$rank["name"]}}</a></td>
                            <td>{{$rank["win"]}}</td>
                            <td>{{$rank["lose"]}}</td>
                            <td>{{$rank["win_diff"]}}</td>
                            <td>{{$rank["win_p"]}}%</td>
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
                    <tr>
                        <td>{{$rank["rank"]}}</td>
                        <td class="team"><a href="team.html"><img src="{{$rank["icon"]}}">{{$rank["name"]}}</a></td>
                        <td>{{$rank["win"]}}</td>
                        <td>{{$rank["lose"]}}</td>
                        <td>{{$rank["win_diff"]}}</td>
                        <td>{{$rank["win_p"]}}%</td>
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
    @component("pc.subject.v2.right_part_cell", ["sl"=>$sl, "articles"=>$comboData["articles"], "videos"=>$comboData["videos"], "season"=>$season, "data"=>$data, "seasons"=>$seasons]) @endcomponent
</div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env("CDN_URL")}}/js/pc/v2/league_nba_2.js"></script>
    <script type="text/javascript">
        var LeagueKeyword = '{{$sl["name_en"]}}';
        var NowDate = '{{date('Y-m-d')}}';//'2019-02-21';
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection