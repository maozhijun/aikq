@if(isset($ranks) && count($ranks) > 0)
    <?php
    if (!isset($subject['type'])) {
        $subject['type'] = 1;
    }
    $name_en = isset($subject["name_en"]) ? $subject["name_en"] : "other";
    ?>

    @if($subject['sport'] == 1)
        @if($subject['type'] == 1)
            <div class="con_box">
                <div class="header_con">
                    <h4>{{$subject["name"]}}积分榜</h4>
                </div>
                <table class="right_table integral">
                    <col width="14.6%"><col><col width="18%"><col width="15%"><col width="15%">
                    <tr>
                        <th>排名</th>
                        <th>球队</th>
                        <th>胜/平/负</th>
                        <th>得/失</th>
                        <th>积分</th>
                    </tr>
                    @foreach($ranks as $rank)
                        <?php
                        $steam = isset($teams[$rank['tid']]) ? $teams[$rank['tid']] : ["icon"=>""];
                            $tUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $rank['sport'], $rank['tid']);
                        ?>
                        <tr>
                            <td>{{$rank['rank']}}</td>
                            @if(isset($rank['tid']))
                                <td class="name"><img src="{{\App\Models\LgMatch\Team::getIcon($steam['icon'])}}"><a target="_blank" href="{{$tUrl}}">{{$rank['name']}}</a></td>
                            @else
                                <td class="name"><img src="{{\App\Models\LgMatch\Team::getIcon($steam['icon'])}}">{{$rank['name']}}</td>
                            @endif
                            <td>{{$rank['win']}}/{{$rank['draw']}}/{{$rank['lose']}}</td>
                            <td>{{isset($rank['goal'])?$rank['goal']:'-'}}/{{isset($rank['fumble']) ? $rank['fumble'] : '-'}}</td>
                            <td>{{$rank['score']}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            //小组没有
        @endif
    @elseif ($subject['sport'] == 2 && $subject['lid'] == 1)
        <div class="con_box">
            <div class="header_con">
                <h4>{{$subject["name"]}}积分榜</h4>
                <a href="javascript:void(0)" class="nba_part on" forItem="west">西部</a>
                <a href="javascript:void(0)" class="nba_part" forItem="east">东部</a>
            </div>
            <table class="right_table integral east" id="East" style="display: none;">
                <col width="14.6%"><col><col width="18%"><col width="15%">
                <tr>
                    <th></th>
                    <th class="name">球队</th>
                    <th>胜/负</th>
                    <th>胜率</th>
                </tr>
                @if(isset($ranks['east']))
                    @foreach($ranks['east'] as $east)
                        <?php
                        $steam = isset($teams[$east['tid']]) ? $teams[$east['tid']] : ["icon"=>""];
                        $tUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $east['sport'], $east['tid']);
                        ?>
                        <tr>
                            <td>{{$east['rank']}}</td>
                            @if(isset($east['tid']))
                                <td class="name"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}"><a target="_blank" href="{{$tUrl}}">{{$east['name']}}</a></td>
                            @else
                                <td class="name"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$east['name']}}</td>
                            @endif
                            <td>{{$east['win']}}/{{$east['lose']}}</td>
                            <td>{{$east['win_p']}}%</td>
                        </tr>
                    @endforeach
                @endif
            </table>
            <table class="right_table integral west" id="West">
                <col width="14.6%"><col><col width="18%"><col width="15%">
                <tr>
                    <th></th>
                    <th class="name">球队</th>
                    <th>胜/负</th>
                    <th>胜率</th>
                </tr>
                @if(isset($ranks['west']))
                    @foreach($ranks['west'] as $west)
                        <?php
                        $steam = isset($teams[$west['tid']]) ? $teams[$west['tid']] : ["icon"=>""];
                        $tUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $west['sport'], $west['tid']);
                        ?>
                        <tr>
                            <td>{{$west['rank']}}</td>
                            @if(isset($west['tid']))
                                <td class="name"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}"><a target="_blank" href="{{$tUrl}}">{{$west['name']}}</a></td>
                            @else
                                <td class="name"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$west['name']}}</td>
                            @endif
                            <td>{{$west['win']}}/{{$west['lose']}}</td>
                            <td>{{$west['win_p']}}%</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    @elseif($subject['sport'] == 2)
        <div class="con_box">
            <div class="header_con">
                <h4>{{$subject["name"]}}排名</h4>
            </div>
            <table class="right_table integral">
                <col width="14.6%"><col><col width="18%"><col width="15%">
                <tr>
                    <th>排名</th>
                    <th>球队</th>
                    <th>胜/负</th>
                    <th>胜率</th>
                </tr>
                @foreach($ranks as $rank)
                    <?php
                    $steam = isset($teams[$rank['tid']]) ? $teams[$rank['tid']] : ["icon"=>""];
                    $tUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrlByNameEn($name_en, $rank['sport'], $rank['tid']);
                    ?>
                    <tr>
                        <td>{{$rank['rank']}}</td>
                        @if(isset($rank['tid']))
                            <td class="name"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}"><a target="_blank" href="{{$tUrl}}">{{$rank['name']}}</a></td>
                        @else
                            <td class="name"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$rank['name']}}</td>
                        @endif
                        <td>{{$rank['win']}}/{{$rank['lose']}}</td>
                        <td>{{$rank['win_p']}}%</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
@endif