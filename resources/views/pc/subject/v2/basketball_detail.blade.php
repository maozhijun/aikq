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
        @if(isset($datas) && isset($datas['playoff']) && count($datas['playoff']) > 0)
            @include('pc.subject.v2.basketball_playoff_cell', ['playoff'=>$datas['playoff'], 'lid'=>$sl['lid']]);
        @endif
        <div class="el_con">
            <div class="header">
                <h3><p>NBA赛程</p></h3>
                <div class="date">
                    <button class="left">前三天</button>
                    <p class="con_text">01-25至01-27</p>
                    <button class="right">后三天</button>
                </div>
            </div>
            <div class="schedule_con">
                <p class="date_text">01月25日（今天）</p>
                <table class="match">
                    <col width="11%"><col><col width="12%"><col><col width="42.5%">
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">已结束</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs">已结束</td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a><a href="live.html" class="live">主播Sam哥</a><a href="live.html" class="live">粤语高清</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs"><span class="living">直播中</span></td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs"><span class="living">直播中</span></td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a><a href="live.html" class="live">主播Sam哥</a><a href="live.html" class="live">粤语高清</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                </table>
                <p class="date_text">01月26日</p>
                <table class="match">
                    <col width="11%"><col><col width="9.3%"><col><col width="42.5%">
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a><a href="live.html" class="live">主播Sam哥</a><a href="live.html" class="live">粤语高清</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                </table>
                <p class="date_text">01月27日</p>
                <table class="match">
                    <col width="11%"><col><col width="9.3%"><col><col width="42.5%">
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a><a href="live.html" class="live">主播Sam哥</a><a href="live.html" class="live">粤语高清</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">立陶宛U17</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">乌克兰U17</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a></td>
                    </tr>
                    <tr>
                        <td>14:50</td>
                        <td class="host"><a href="team.html">新西兰破坏者</a></td>
                        <td class="vs">vs</td>
                        <td class="away"><a href="team.html">坎斯大班</a></td>
                        <td class="line"><a href="live.html" class="live">高清直播</a><a href="live.html" class="live">主播剧本球童</a><a href="live.html" class="live">高清直播2</a></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="el_con">
            <div class="header">
                <h3><p>NBA排名</p></h3>
            </div>
            <div class="rank_con">
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
                <p class="date_text">西部</p>
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
    {{--@component("pc.subject.v2.right_part_cell", ["sl"=>$sl, "articles"=>$comboData["articles"], "videos"=>$comboData["videos"], "season"=>$season, "data"=>$data]) @endcomponent--}}
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