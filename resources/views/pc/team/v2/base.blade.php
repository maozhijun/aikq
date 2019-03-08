@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?201903071908">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/team_2.css?time=20192191536">
    @yield('teamCss')
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <?php
                $sport = ($zhuanti['name_en'] == 'nba' || $zhuanti['name_en'] == 'cba')?2:1;
                    $teamIndex = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport,$zhuanti['lid'],$team['id']);
                ?>
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="{{$teamIndex}}">{{$team['name']}}</a>
            </div>
        </div>
    @endif
    <?php
    $coach = "-";
    $sTitle = "";
    ?>
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div id="Team_info">
                <p class="img_con"><img src="{{$team['icon']}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"'></p>
                <h1>{{$team['name']}}</h1>
                <div class="other_info">
                    <p>现任主教练：{{$coach}}</p>
                    <p>外文队名：{{isset($team['name_en']) ? $team['name_en'] :'-'}}</p>
                    <p>所在城市：{{$team['city']}}</p>
                    <p>球队主场：{{$team['gym']}}</p>
                    <p>成立时间：{{strlen($team['establish']) > 0 ? $team['establish'] : "-"}}</p>
                    {{--<p>所属联赛：英超（英格兰超级联赛）</p>--}}
                </div>
            </div>
            @yield('detail')
        </div>
        <div id="Right_part">
            @if(isset($comboData) && isset($zhuanti))
                @include('pc.cell.v2.right_league_cell', ['zhuanti'=>$zhuanti])
                <div class="con_box">
                    <div class="header_con">
                        <h4>最近直播</h4>
                        <a href="/">全部直播</a>
                    </div>
                    <div class="live">
                        @if(isset($comboData['matches']))
                            @foreach($comboData['matches'] as $match)
                                @include('pc.cell.v2.right_match_cell', ['match'=>$match])
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
            @yield('right')
        </div>
    </div>
@endsection
@section('js')
    @yield('teamJs')
    {{--<script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/live_2.js"></script>--}}
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/team_other_2.js"></script>
    <script type="text/javascript">
        var LeagueKeyword = '{{isset($zhuanti) ? $zhuanti['name_en'] : 'all'}}';
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection