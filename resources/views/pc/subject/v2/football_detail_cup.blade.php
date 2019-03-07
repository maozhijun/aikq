@extends("pc.layout.v2.base")
<?php $cdnUrl = env("CDN_URL"); ?>
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/left_right_2.css">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/league_2.css">
@endsection
@section("content")
    <div id="Crumbs">
        <div class="def_content">
            <a href="/">爱看球</a> - {{$sl["name"]}}
        </div>
    </div>
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div class="knockout_con football">
                <div class="round_con">
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
                <div class="round_con">
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
                <a class="finals_match" href="live.html">
                    <img src="{{$cdnUrl}}/img/pc/image_football_n.png" class="cup">
                    <div class="team_con">
                        <p class="team"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/15.png"><span>湖人</span></p>
                        <p class="score">3&nbsp;&nbsp;&nbsp;2</p>
                        <p class="team"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/15.png"><span>凯尔特人</span></p>
                    </div>
                </a>
            </div>

            <div class="el_con">
                <div class="header">
                    <h3><p>{{$sl["name"]}}赛程</p></h3>
                </div>
                <div class="schedule_con">
                    <div class="round_con">
                        <div class="item_box cup">
                            @foreach($stages as $stage)
                                <p @if($stage["status"] == 1) class="on" @endif >{{$stage["name"]}}</p>
                            @endforeach
                        </div>
                    </div>
                    <table class="match">
                        <colgroup>
                            <col width="11%"><col><col width="12%"><col><col width="42.5%">
                        </colgroup>
                        <tbody>
                        @foreach($schedules as $match)
                        <?php
                            $status = $match["status"]; $sport = $sl["sport"]; $lid = $sl["lid"];
                            $hTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $match["hid"]);
                            $aTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $match["aid"]);
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $match["id"]);
                            $matchLive = \App\Models\Match\MatchLive::getMatchLiveByMid($sport, $match["id"]);
                        ?>
                        <tr>
                            <td>{{substr($match["time"], 5, 11)}}</td>
                            <td class="host"><a target="_blank" href="{{$hTeamUrl}}">{{$match["hname"]}}</a></td>
                            <td class="vs">
                                @if($status > 0 && isset($matchLive)) <span class="living">直播中</span>
                                @elseif($status == -1 || $status > 0) {{$match["hscore"] . " - " . $match["ascore"]}}
                                @else vs
                                @endif
                            </td>
                            <td class="away"><a target="_blank" href="{{$aTeamUrl}}">{{$match["aname"]}}</a></td>
                            <td class="line">
                                @if(isset($matchLive))<a target="_blank" href="{{$liveUrl}}" class="live">观看直播</a>@endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(isset($ranks) && count($ranks) > 0)
            <div class="el_con">
                <div class="header">
                    <h3><p>积分排名</p></h3>
                </div>
                <div class="rank_con">
                    @foreach($ranks as $g=>$scores)
                        <p class="date_text">{{$g}}组</p>
                        <table class="rank">
                            <colgroup>
                                <col width="7.8%"><col><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="10%"><col width="10%">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>排名</th>
                                <th class="team">球队</th>
                                <th>赛</th>
                                <th>胜</th>
                                <th>平</th>
                                <th>负</th>
                                <th>进球</th>
                                <th>失球</th>
                                <th>净胜球</th>
                                <th>积分</th>
                            </tr>
                            @foreach($scores as $index=>$score)
                                <?php
                                    if (isset($score['tid'])) {
                                        $teamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($score['sport'], $score['lid'], $score['tid']);
                                    } else {
                                        $teamUrl = "#";
                                    }
                                    $icon = \App\Models\Match\Team::getIcon($score["icon"]);
                                ?>
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td class="team"><a target="_blank" href="{{$teamUrl}}"><img src="{{$icon}}">{{$score['name']}}</a></td>
                                    <td>{{$score["count"]}}</td>
                                    <td>{{$score['win']}}</td>
                                    <td>{{$score['draw']}}</td>
                                    <td>{{$score['lose']}}</td>
                                    <td>{{$score["goal"]}}</td>
                                    <td>{{$score["fumble"]}}</td>
                                    <td>{{$score["goal"] - $score["fumble"]}}</td>
                                    <td>{{$score['score']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div id="Right_part">
            <div id="League_info">
                <div class="info_con">
                    <img src="{{$sl["icon"]}}">
                    <h1>{{$sl["name"]}}（{{$sl["name_long"]}}）</h1>
                </div>
                <div class="season_con">
                    <p>{{$season["name"]}} 赛季</p>
                    {{--<dl>--}}
                    {{--<dt>切换赛季</dt>--}}
                    {{--<dd style="display: none;">--}}
                    {{--<a href="#" class="on">2018 - 2019赛季</a>--}}
                    {{--<a href="#">2017 - 2018赛季</a>--}}
                    {{--<a href="#">2016 - 2017赛季</a>--}}
                    {{--<a href="#">2015 - 2016赛季</a>--}}
                    {{--<a href="#">2014 - 2015赛季</a>--}}
                    {{--<a href="#">2013 - 2014赛季</a>--}}
                    {{--<a href="#">2012 - 2013赛季</a>--}}
                    {{--<a href="#">2011 - 2012赛季</a>--}}
                    {{--</dd>--}}
                    {{--</dl>--}}
                </div>
            </div>
            @if(isset($comboData["articles"]) && count($comboData["articles"]) > 0 )
                <div class="con_box">
                    <div class="header_con">
                        <h4>{{$sl["name"]}}资讯</h4>
                        <a target="_blank" href="/{{$sl["name_en"]}}/news/">全部{{$sl["name"]}}资讯</a>
                    </div>
                    <div class="news">
                        @foreach($comboData["articles"] as $index=>$article)
                            @if($index < 2)
                                <a target="_blank" href="{{$article["link"]}}" class="img_news">
                                    <p class="img_box"><img src="{{$article["cover"]}}"></p>
                                    <h3>{{$article["title"]}}</h3>
                                </a>
                            @else
                                <a target="_blank" href="{{$article["link"]}}" class="text_new"><h4>{{$article["title"]}}</h4></a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
            @if(isset($comboData["videos"]) && count($comboData["videos"]) > 0)
                <div class="con_box">
                    <div class="header_con">
                        <h4>{{$sl["name"]}}视频</h4>
                        <a href="/{{$sl["name_en"]}}/video">{{$sl["name"]}}视频集锦</a>
                        <a href="/{{$sl["name_en"]}}/record">{{$sl["name"]}}比赛录像</a>
                    </div>
                    <div class="video">
                        @foreach($comboData["videos"] as $video)
                            <div class="video_item">
                                <a target="_blank" href="{{$video["link"]}}">
                                    <p class="img_box"><img src="{{$video["image"]}}"></p>
                                    <p class="text_box">{{$video["title"]}}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(isset($data))
            <div class="con_box">
                <div class="header_con">
                    <h4>球员数据</h4>
                    <a href="/{{$sl["name_en"]}}/data/">{{$sl["name"]}}详细数据</a>
                </div>
                <div class="player_rank">
                    <div class="rank_tab_box">
                        <p class="on" foritem="goal">进球</p>
                        <p foritem="assist">助攻</p>
                        <p foritem="yellow">黄牌</p>
                        <p foritem="red">红牌</p>
                    </div>
                    @if(isset($data["goal"]))
                    <table class="goal">
                        <colgroup><col width="15%"><col><col width="30%"></colgroup>
                        <tbody>
                        @foreach($data["goal"] as $index=>$goal)
                        <tr>
                            <td class="num">{{$index + 1}}</td>
                            <td>
                                <p class="name">{{$goal["pname"]}}</p>
                            </td>
                            <td class="score">{{$goal["value"]}}@if($goal["penalty"] > 0)<span>(点球：{{$goal["penalty"]}})</span>@endif</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($data["assist"]))
                    <table class="assist" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["assist"] as $index=>$assist)
                        <tr>
                            <td class="num">{{$index + 1}}</td>
                            <td>
                                <p class="name">{{$assist["pname"]}}</p>
                            </td>
                            <td class="score">{{$assist["value"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($data["yellow"]))
                    <table class="yellow" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["yellow"] as $index=>$yellow)
                        <tr>
                            <td class="num">{{$index + 1}}</td>
                            <td>
                                <p class="name">{{$yellow["pname"]}}</p>
                            </td>
                            <td class="score">{{$yellow["value"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($data["red"]))
                    <table class="red" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["red"] as $index=>$red)
                        <tr>
                            <td class="num">{{$index + 1}}</td>
                            <td>
                                <p class="name">{{$red["pname"]}}</p>
                            </td>
                            <td class="score">{{$red["value"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env("CDN_URL")}}/js/pc/v2/league_cup_2.js"></script>
    <script type="text/javascript">
        var LeagueKeyword = '{{$sl["name_en"]}}';
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection