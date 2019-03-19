@extends("pc.layout.v2.base")
<?php $cdnUrl = env("CDN_URL"); ?>
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/left_right_2.css?201903181056">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/league_2.css?201903141015">
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
                            for ($index = 1; $index <= $season["total_round"]; $index++) {
                                echo "<p forItem='".$index."'" . ($index == $curRound ? " class=\"on\" " : "") . ">".$index."</p>";
                            }
                        ?>
                    </div>
                </div>
                @if(isset($schedule))
                <?php
                    $sport = $sl["sport"];
                    $lid = $sl["lid"];
                    $teamUrlArray = [];
                    $detailArray = [];
                ?>
                @foreach($schedule as $round=>$matches)
                    <table class="match" round="{{$round}}" @if($round != $curRound) style="display: none;" @endif >
                        <colgroup><col width="25%"><col><col width="12%"><col><col width="20%"></colgroup>
                        @foreach($matches as $match)
                        <?php
                            $mid = $match["mid"];
                            $time = date("Y-m-d H:i", $match["time"]);
                            $hid = $match["hid"];
                            $aid = $match["aid"];
                            $status = $match["status"];
                            $name_en = $sl["name_en"];
                            $detailKey = $hid > $aid ? ($hid . "_" . $aid) : ($aid . "_" . $hid);

                            if (!isset($teamUrlArray[$hid])) {
                                $hTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $sport, $hid);
                                $teamUrlArray[$hid] = $hTeamUrl;
                            } else {
                                $hTeamUrl = $teamUrlArray[$hid];
                            }
                            if (!isset($teamUrlArray[$aid])) {
                                $aTeamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $sport, $aid);
                                $teamUrlArray[$aid] = $aTeamUrl;
                            } else {
                                $aTeamUrl = $teamUrlArray[$aid];
                            }
                            if (isset($detailArray[$detailKey])) {
                                $detailUrl = $detailArray[$detailKey];
                            } else {
                                $detailUrl = "/".$sl["name_en"]."/live".$sport.\App\Http\Controllers\PC\CommonTool::getMatchVsByTid($hid, $aid, $mid).".html";
                            }
                            if (empty($hid)) $detailUrl = "javascript:void(0);";
                            if ($status < 0)  $detailUrl = "javascript:void(0);";
                            if (empty($aid)) $aTeamUrl = "javascript:void(0);";
                            if (empty($hid)) $hTeamUrl = "javascript:void(0);";
                        ?>
                        <tr>
                            <td><span>{{$time}}</span></td>
                            <td class="host"><a href="{{$hTeamUrl}}">{{$match["hname"]}}</a></td>
                            <td class="vs">
                                @if($match["status"] == -1 || $match["status"] > 0)
                                    @if($match["status"] > 0) <span class="living">直播中</span> @else {{$match["hscore"] . " - " . $match["ascore"]}} @endif
                                @else vs  @endif
                            </td>
                            <td class="away"><a href="{{$aTeamUrl}}">{{$match["aname"]}}</a></td>
                            <td class="line">
                                @if(!empty($detailUrl))<a href="{{$detailUrl}}" class="live">观看直播</a>@endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                @endforeach
                @endif
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
            <?php $dataCount = 9; ?>
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
                            @break($index > $dataCount)
                            <tr><td>{{$index + 1}} {{$goal["pname"]}}</td><td>{{$goal["value"]}}</td><td>{{$goal["penalty"]}}</td></tr>
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
                            @break($index > $dataCount)
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
                            @break($index > $dataCount)
                            <tr><td>{{$index+1}} {{$yellow["pname"]}}</td><td>{{$yellow["value"]}}</td></tr>
                            @endforeach
                        </table>
                        <table class="red" style="display: none;">
                            <col><col width="35%">
                            <tr><th>球员</th><th>红牌</th></tr>
                            @foreach($data["red"] as $index=>$red)
                            @break($index > $dataCount)
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
    @include("pc.subject.v2.right_part_cell", ["sl"=>$sl, "articles"=>isset($comboData["articles"])?$comboData["articles"]:array(),
        "videos"=>isset($comboData["videos"])?$comboData["videos"]:array(), "season"=>$season, "isLeague"=>true])
</div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env("CDN_URL")}}/js/pc/v2/league_2.js"></script>
    <script type="text/javascript">
    var LeagueKeyword = '{{$sl["name_en"]}}';
    window.onload = function () { //需要添加的监控放在这里
        setPage();
        $("div.season_con dt").click(function () {
            var $next = $(this).next();
            if ($next.css("display") != "none") {
                $next.css("display", "none");
            } else {
                $next.css("display", "");
            }

        });
    }
</script>
@endsection