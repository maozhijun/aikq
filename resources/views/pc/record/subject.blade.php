@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/record_list_2.css?201903071908">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/record_league_2.css?time=20192191536">
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$zhuanti['name']}}录像
            </div>
        </div>
    @endif
    <div class="def_content" id="Content">
        <div id="01_24" class="el_con">
            <div class="header">
                <h3><p>{{$zhuanti['name']}}录像</p></h3>
            </div>
            <div class="team_con">
                @if($zhuanti['name_en'] == 'nba')
                    <div class="team_part" style="display: ;">
                        <b class="title_text">西部</b>
                        @foreach($teams['west'] as $tid)
                            <?php
                            $team = $teamsData[$tid];
                                $turl = \App\Http\Controllers\PC\CommonTool::getTeamRecordUrl(2,1,$tid);
                            ?>
                            @if(isset($team))
                                <p class="team_box"><a href="{{$turl}}"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($team['icon'])}}">{{$team['name_china_short']}}</a></p>
                            @endif
                        @endforeach
                    </div>
                    <div class="team_part" style="display: ;">
                        <b class="title_text">东部</b>
                        @foreach($teams['east'] as $tid)
                            <?php
                            $team = $teamsData[$tid];
                            $turl = \App\Http\Controllers\PC\CommonTool::getTeamRecordUrl(2,1,$tid);
                            ?>
                            @if(isset($team))
                                <p class="team_box"><a href="{{$turl}}"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($team['icon'])}}">{{$team['name_china_short']}}</a></p>
                            @endif
                        @endforeach
                    </div>
                @elseif($zhuanti['name_en'] == 'cba')
                    <div class="team_part" style="display: ;">
                        @foreach($teams as $tid)
                            <?php
                            $team = $teamsData[$tid];
                            $turl = \App\Http\Controllers\PC\CommonTool::getTeamRecordUrl(2,2,$tid);
                            ?>
                            @if(isset($team))
                                <p class="team_box"><a href="{{$turl}}"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($team['icon'])}}">{{$team['name_china_short']}}</a></p>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="team_part" style="display: ;">
                        @foreach($teams as $tid)
                            <?php
                            $team = $teamsData[$tid];
                            $turl = \App\Http\Controllers\PC\CommonTool::getTeamRecordUrl(1,$zhuanti['lid'],$tid);
                            ?>
                            @if(isset($team))
                                <p class="team_box"><a href="{{$turl}}"><img src="{{\App\Models\LgMatch\Team::getIcon($team['icon'])}}">{{$team['name']}}</a></p>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
            <table>
                <col width="0%"><col width="12.4%"><col width="14%"><col><col width="15%"><col><col width="20%">
                @foreach($records as $record)
                    <?php
                    $type = $record['sport'] == 1 ? 'foot' : 'basket';
                    $timeStr = date('Y-m-d H:i',date_create($record['time'])->getTimestamp());
                    $subject = isset($subjects[$record['s_lid']])? $subjects[$record['s_lid']]['name_en'] : 'other';
                    $subjectCN = isset($subjects[$record['s_lid']])? $subjects[$record['s_lid']]['name'] : $record['lname'];

                    $hurl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($record['sport'],$record['s_lid'],$record['hid']);
                    $aurl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($record['sport'],$record['s_lid'],$record['aid']);
                    ?>
                    <tr type="{{$type}}ball">
                        <td><img class="icon" src="{{env('CDN_URL')}}/img/pc/v2/icon_{{$type}}_light_opaque.png"></td>
                        <td>{{$subjectCN}}</td>
                        <td>{{$timeStr}}</td>
                        <td><a href="{{$hurl}}">{{$record['hname']}}</a></td>
                        <td>{{$record['hscore']}} - {{$record['ascore']}}</td>
                        <td><a href="{{$aurl}}">{{$record['aname']}}</a></td>
                        <td class="channel"><a target="_blank" href="/{{$subject}}/record{{$record['mid']}}.html">观看录像</a></td>
                    </tr>
                @endforeach
            </table>
            @if($page > 1)
                @component("pc.layout.v2.page_cell", ['lastPage'=>$page, "curPage"=>$pageNo,'href'=>'/'.$zhuanti['name_en'].'/record/index']) @endcomponent
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/record_list_2.js?201903111750"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
//            setPage();
        }
    </script>
@endsection