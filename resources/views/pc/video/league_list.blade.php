@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/video_list_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/video_league_2.css">
@endsection
@section('content')
    <div class="def_content">
        <div class="el_con">
            <div class="header">
                <h3><p>{{$zhuanti['name']}}</p></h3>
                <p class="aline">
                    <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}专区 ></a>
                </p>
            </div>
        </div>
        <div class="team_con">
            @if($zhuanti['name_en'] == 'nba')
                <div class="team_part" style="display: ;">
                    <b class="title_text">东部</b>
                    @foreach($teams['east'] as $item)
                        <?php
                        $team = $teamsData[$item];
                        ?>
                        <p class="team_box"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamVideoUrlByNameEn(2, $team['id'], "nba")}}"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($team['icon'])}}">{{$team['name_china_short']}}</a></p>
                    @endforeach
                </div>
                <div class="team_part" style="display: ;">
                    <b class="title_text">西部</b>
                    @foreach($teams['west'] as $item)
                        <?php
                        $team = $teamsData[$item];
                        ?>
                        <p class="team_box"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamVideoUrlByNameEn(2, $team['id'], "nba")}}"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($team['icon'])}}">{{$team['name_china_short']}}</a></p>
                    @endforeach
                </div>
            @else
                <div class="team_part" style="">
                    @foreach($teams as $item)
                        <?php
                        $team = $teamsData[$item];
                        $sport = $zhuanti['name_en'] == 'cba' ? 2 : 1;
                        if ($sport == 2){
                            $icon = \App\Models\LgMatch\BasketTeam::getIcon($team['icon']);
                            $teamName = $team['name_china'];
                        }
                        else{
                            $icon =  \App\Models\LgMatch\Team::getIcon($team['icon']);
                            $teamName = $team['name'];
                        }
                        ?>
                        <p class="team_box"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamVideoUrlByNameEn($sport, $team['id'], $zhuanti['name_en'])}}"><img src="{{$icon}}">{{$teamName}}</a></p>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="video_con">
            @foreach($tags as $tag)
                @if($type == $tag["name_en"])
                    <?php $pageUrl = "/" . $tag["name_en"]."/video" ?>
                @endif
            @endforeach
            @foreach($videos as $video)
                <div class="item_con">
                    <a target="_blank" href="{{\App\Models\Match\HotVideo::getVideoDetailUrl($video["id"])}}">
                        <img src="{{$video["cover"]}}">
                        <p>{{$video["title"]}}</p>
                    </a>
                </div>
            @endforeach
            @component("pc.video.page_cell", ["page"=>$page, "pageUrl"=>isset($pageUrl) ? $pageUrl : ""]) @endcomponent
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/video_list_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection