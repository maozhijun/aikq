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
            @if(isset($data))
            <div class="data_con">
                @if(isset($data["goal"]))
                <div class="con_in" style="width: 40%;">
                    <div class="title">
                        <h4>射手榜</h4>
                    </div>
                    <div class="tab_box">
                        <table>
                            <col><col width="22%"><col width="22%">
                            <tr><th>球员</th><th>进球</th><th>点球</th></tr>
                            @foreach($data["goal"] as $index=>$goal)
                            <tr><td><a href="">{{$index + 1}} {{$goal["pname"]}}</a></td><td>{{$goal["value"]}}</td><td>{{$goal["penalty"]}}</td></tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif
                @if(isset($data["assist"]))
                <div class="con_in" style="width: 30%;">
                    <div class="title">
                        <h4>助攻榜</h4>
                    </div>
                    <div class="tab_box">
                        <table class="score">
                            <col><col width="35%">
                            <tr><th>球员</th><th>助攻</th></tr>
                            @foreach($data["assist"] as $index=>$assist)
                            <tr><td>{{$index + 1}} {{$assist["pname"]}}</td><td>{{$assist["value"]}}</td></tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif
                @if(isset($data["red"]) && isset($data["yellow"]))
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
                            @foreach($data["yellow"] as $index=>$yellow)
                            <tr><td>{{$index+1}} {{$yellow["pname"]}}</td><td>{{$yellow["value"]}}</td></tr>
                            @endforeach
                        </table>
                        <table class="red" style="display: none;">
                            <col><col width="35%">
                            <tr><th>球员</th><th>红牌</th></tr>
                            @foreach($data["red"] as $index=>$red)
                            <tr><td>{{$index+1}} {{$red["pname"]}}</td><td>{{$red["value"]}}</td></tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @component("pc.subject.v2.right_part_cell", ["sl"=>$sl, "articles"=>$comboData["articles"], "videos"=>$comboData["videos"], "season"=>$season]) @endcomponent
</div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env("CDN_URL")}}/js/pc/v2/league_2.js"></script>
    <script type="text/javascript">
    var LeagueKeyword = '{{$sl["name_en"]}}';
    window.onload = function () { //需要添加的监控放在这里
        setPage();
    }
</script>
@endsection