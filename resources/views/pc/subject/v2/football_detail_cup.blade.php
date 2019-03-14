@extends("pc.layout.v2.base")
<?php $cdnUrl = env("CDN_URL"); ?>
@section("css")
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/left_right_2.css?201903071908">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/league_2.css?201903141016">
@endsection
@section("content")
    <div id="Crumbs">
        <div class="def_content">
            <a href="/">爱看球</a> - {{$sl["name"]}}
        </div>
    </div>
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            @if(isset($knockouts))
            <div class="knockout_con football">
                <div class="round_con">
                    @component("pc.subject.v2.football_detail_cup_kk", ["knockouts"=>$knockouts, "count"=>16, "sl"=>$sl, "p"=>"before"]) @endcomponent
                    <div class="line_left_con" style="height: 82px; top: 32px; left: 10px;"></div>
                    <div class="line_left_con" style="height: 82px; top: 210px; left: 10px;"></div>
                </div>
                <div class="round_con">
                    @component("pc.subject.v2.football_detail_cup_kk", ["knockouts"=>$knockouts, "count"=>8, "sl"=>$sl, "p"=>"before"]) @endcomponent
                    <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
                </div>
                <div class="round_con">
                    @component("pc.subject.v2.football_detail_cup_kk", ["knockouts"=>$knockouts, "count"=>4, "sl"=>$sl, "p"=>"before"]) @endcomponent
                    <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
                </div>
                <div class="round_con">
                    @component("pc.subject.v2.football_detail_cup_kk", ["knockouts"=>$knockouts, "count"=>4, "sl"=>$sl, "p"=>"after"]) @endcomponent
                    <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
                </div>
                <div class="round_con">
                    @component("pc.subject.v2.football_detail_cup_kk", ["knockouts"=>$knockouts, "count"=>8, "sl"=>$sl, "p"=>"after"]) @endcomponent
                    <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
                </div>
                <div class="round_con">
                    @component("pc.subject.v2.football_detail_cup_kk", ["knockouts"=>$knockouts, "count"=>16, "sl"=>$sl, "p"=>"after"]) @endcomponent
                    <div class="line_right_con" style="height: 82px; top: 32px; right: 10px;"></div>
                    <div class="line_right_con" style="height: 82px; top: 210px; right: 10px;"></div>
                </div>
                @if(isset($knockouts[2]))
                <?php
                    $mid = "";$hid = "";$aid = "";
                    foreach ($knockouts[2] as $final) {
                        $mid = $final["host"]["mid"];
                        $hid = $final["host"]["id"];
                        $aid = $final["away"]["id"];
                        break;
                    }
                ?>
                <a class="finals_match" @if(!empty($hid)) href="{{\App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sl["sport"], $sl["lid"], $mid)}}" @endif >
                    <img src="{{$cdnUrl}}/img/pc/image_football_n.png" class="cup">
                    @if(empty($hid))
                        <div class="team_con">
                            <p class="team"></p><p class="team"></p>
                        </div>
                    @else
                        <div class="team_con">
                            <p class="team"><img src="{{\App\Models\Match\Team::getIconById($hid)}}"><span>{{$final["host"]["name"]}}</span></p>
                            <p class="score">@if(isset($final["host"]["score"])) {{$final["host"]["score"]}}&nbsp;&nbsp;&nbsp;{{$final["away"]["score"]}} @else @endif</p>
                            <p class="team"><img src="{{\App\Models\Match\Team::getIconById($aid)}}"><span>{{$final["away"]["name"]}}</span></p>
                        </div>
                    @endif
                </a>
                @endif
            </div>
            @endif
            @if(isset($schedules))
            <div class="el_con">
                <div class="header">
                    <h3><p>{{$sl["name"]}}赛程</p></h3>
                </div>
                <div class="schedule_con">
                    <div class="round_con">
                        <div class="item_box cup">
                            @foreach($stages as $stage)
                                <?php
                                    $on = $stage["status"] == 1;
                                    $forItem = $stage["id"] . ($stage["name"] == "分组赛" ? "-A" : "");
                                ?>
                                <p @if($on) class="on" @endif forItem="{{$forItem}}" >
                                    {{$stage["name"]}}
                                    @if($stage["name"] == "分组赛")
                                    <?php
                                        $sId = $stage["id"];
                                        $group = $stage["group"];
                                        $gLen = strlen($group);
                                        for ($gIndex = 0; $gIndex < $gLen; $gIndex++) {
                                            $g = substr($group, $gIndex, 1);
                                            echo "<span forItem=\"".($sId."-".$g)."\" ". ($gIndex == 0 && $on ? "class=\"on\"" : "") ." >".$g."</span>" . ($gIndex + 1 == $gLen ? "" : "/");
                                        }
                                    ?>
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                    @foreach($schedules as $stageId=>$schedule)
                    <?php $hasGroup = isset($schedule["groupMatches"]) && count($schedule["groupMatches"]) > 0;?>
                        @if($hasGroup)
                            <?php $gIndex = 0; ?>
                            @foreach($schedule["groupMatches"] as $g=>$groupMatch)
                                @component("pc.subject.v2.football_detail_cup_schedule", ["sl"=>$sl, "round"=>$stageId."-".$g, "schMatches"=>$groupMatch["matches"], "status"=>($schedule["status"] == 1 && $gIndex++ == 0) ? 1 : 0]) @endcomponent
                            @endforeach
                        @else
                            @component("pc.subject.v2.football_detail_cup_schedule", ["sl"=>$sl, "round"=>$stageId, "schMatches"=>$schedule["matches"], "status"=>$schedule["status"]]) @endcomponent
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
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
        @include("pc.subject.v2.right_part_cell", ["sl"=>$sl, "articles"=>isset($comboData["articles"])?$comboData["articles"]:array(), "videos"=>isset($comboData["videos"])?$comboData["videos"]:array(), "season"=>$season, "data"=>$data, "seasons"=>$seasons])
    </div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env("CDN_URL")}}/js/pc/v2/league_cup_2.js"></script>
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