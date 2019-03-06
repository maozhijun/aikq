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
        <div class="el_con">
            <div class="header">
                <h3><p>{{$sl["name"]}}赛程</p></h3>
            </div>
            <div class="schedule_con">
                <div class="round_con">
                    <div class="item_box">
                        <?php
                            for ($index = 1; $index < $season["total_round"]; $index++) {
                                echo "<p" . ($index == $round ? " class=\"on\" " : "") . ">".$index."</p>";
                            }
                        ?>
                    </div>
                </div>
                <table class="match">
                    <col width="11%"><col><col width="12%"><col><col width="42.5%">
                    @foreach($rounds as $round)
                        <?php
                            $mid = $round["id"];
                            $matchLive = \App\Models\Match\MatchLive::getMatchLiveByMid($sl["sport"], $mid);
                        ?>
                    <tr>
                        <td><span>{{substr($round["time"], 5, 5)}}</span><br/>{{substr($round["time"], 11, 5)}}</td>
                        <td class="host"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sl["sport"], $round["lid"], $round["hid"])}}">{{$round["hname"]}}</a></td>
                        <td class="vs">
                            @if($round["status"] == -1 || $round["status"] > 0)
                                @if(isset($matchLive) && $round["status"] > 0) <span class="living">直播中</span> @else {{$round["hscore"] . " - " . $round["ascore"]}} @endif
                            @else vs  @endif
                        </td>
                        <td class="away"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sl["sport"], $round["lid"], $round["aid"])}}">{{$round["aname"]}}</a></td>
                        <td class="line">
                            @if(isset($matchLive))
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sl["sport"], $sl["lid"], $mid)}}" class="live">观看直播</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="el_con">
            <div class="header">
                <h3><p>{{$sl["name"]}}积分</p></h3>
                <p class="aline">
                    <a target="_blank" href="/{{$sl["name_en"]}}/data/">详细{{$sl["name"]}}数据 ></a>
                </p>
            </div>
            <div class="rank_con">
                <table class="rank">
                    <col width="7.8%"><col><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="10%"><col width="10%">
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
                    @foreach($scores as $score)
                    <tr>
                        <td>{{$score["rank"]}}</td>
                        <td class="team">
                            <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sl["sport"], $sl["lid"], $score["tid"])}}"><img src="{{\App\Models\Match\Team::getIcon($score["ticon"])}}">{{$score["tname"]}}</a>
                        </td>
                        <td>{{$score["count"]}}</td>
                        <td>{{$score["win"]}}</td>
                        <td>{{$score["draw"]}}</td>
                        <td>{{$score["lose"]}}</td>
                        <td>{{$score["goal"]}}</td>
                        <td>{{$score["fumble"]}}</td>
                        <td>{{$score["goal"] - $score["fumble"]}}</td>
                        <td>{{$score["score"]}}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="el_con">
            <div class="header">
                <h3><p>数据榜单</p></h3>
                <p class="aline">
                    <a target="_blank" href="/{{$sl["name_en"]}}/data/">详细{{$sl["name"]}}数据 ></a>
                </p>
            </div>
            <div class="data_con">
                <div class="con_in" style="width: 40%;">
                    <div class="title">
                        <h4>射手榜</h4>
                    </div>
                    <div class="tab_box">
                        <table>
                            <col><col width="22%"><col width="22%">
                            <tr><th>球员</th><th>进球</th><th>点球</th></tr>
                            <tr><td><a href="">1 阿扎尔</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">2 梅西</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">3 阿扎尔</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">4 梅西</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">5 阿扎尔</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">6 梅西</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">7 阿扎尔</a></td><td>34</td><td>12</td></tr>
                            <tr><td><a href="">8 梅西</a></td><td>34</td><td>12</td></tr>
                        </table>
                    </div>
                </div>
                <div class="con_in" style="width: 30%;">
                    <div class="title">
                        <h4>助攻榜</h4>
                    </div>
                    <div class="tab_box">
                        <table class="score">
                            <col><col width="35%">
                            <tr><th>球员</th><th>助攻</th></tr>
                            <tr><td>1 詹姆斯哈登</td><td>10</td></tr>
                            <tr><td>2 斯蒂芬·库里</td><td>10</td></tr>
                            <tr><td>3 安东尼·戴维斯</td><td>10</td></tr>
                            <tr><td>4 费得了</td><td>10</td></tr>
                            <tr><td>5 波士顿人</td><td>10</td></tr>
                            <tr><td>6 布鲁</td><td>10</td></tr>
                            <tr><td>7 夏洛特</td><td>10</td></tr>
                            <tr><td>8 阿密热</td><td>10</td></tr>
                        </table>
                    </div>
                </div>
                <div class="con_in" style="width: 30%;">
                    <div class="title">
                        <h4>红黄牌</h4>
                        <p class="tab_item" forItem="red">红牌</p>
                        <p class="tab_item on" forItem="yellow">黄牌</p>
                    </div>
                    <div class="tab_box">
                        <table class="yellow">
                            <col><col width="35%">
                            <tr><th>球员</th><th>黄牌</th></tr>
                            <tr><td>1 詹姆斯哈登</td><td>10</td></tr>
                            <tr><td>2 斯蒂芬·库里</td><td>10</td></tr>
                            <tr><td>3 安东尼·戴维斯</td><td>10</td></tr>
                            <tr><td>4 费得了</td><td>10</td></tr>
                            <tr><td>5 波士顿人</td><td>10</td></tr>
                            <tr><td>6 布鲁</td><td>10</td></tr>
                            <tr><td>7 夏洛特</td><td>10</td></tr>
                            <tr><td>8 阿密热</td><td>10</td></tr>
                        </table>
                        <table class="red" style="display: none;">
                            <col><col width="35%">
                            <tr><th>球员</th><th>红牌</th></tr>
                            <tr><td>1 詹姆斯哈登</td><td>10</td></tr>
                            <tr><td>2 斯蒂芬·库里</td><td>10</td></tr>
                            <tr><td>3 安东尼·戴维斯</td><td>10</td></tr>
                            <tr><td>4 费得了</td><td>10</td></tr>
                            <tr><td>5 波士顿人</td><td>10</td></tr>
                            <tr><td>6 布鲁</td><td>10</td></tr>
                            <tr><td>7 夏洛特</td><td>10</td></tr>
                            <tr><td>8 阿密热</td><td>10</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                <a href="news_league.html">全部{{$sl["name"]}}资讯</a>
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
    </div>
</div>
@endsection
@section("js")
<script type="text/javascript">
    var LeagueKeyword = 'yingchao';
    window.onload = function () { //需要添加的监控放在这里
        setPage();
    }
</script>
@endsection