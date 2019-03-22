@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?201903221050">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/live_list_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/league_nba_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/home_2.css?201903221050">
@endsection
@section('content')
    <div class="def_content" id="Content">
        <div id="Focus">
            <div class="news">
                @for($i = 1 ; $i < min(14,count($articles)) ; $i++)
                    @if($i == 1 || $i == 6 || $i == 10)
                        <h2><a target="_blank" href="{{$articles[$i]['link']}}">{{$articles[$i]['title']}}</a></h2>
                    @else
                        <p><a target="_blank" href="{{$articles[$i]['link']}}">{{$articles[$i]['title']}}</a></p>
                    @endif
                @endfor
            </div>
            <div class="focus">
                @if(count($articles) > 0)
                    <div class="first_con">
                        <a target="_blank" href="{{$articles[0]['link']}}">
                            <img src="{{$articles[0]['cover']}}">
                            <p>{{$articles[0]['title']}}</p>
                        </a>
                    </div>
                @endif
                @if(count($videos) > 1)
                    <div class="second_con">
                        <a href="{{$videos[0]['link']}}">
                            <img src="{{$videos[0]['image']}}">
                            <p>{{$videos[0]['title']}}</p>
                        </a>
                    </div>
                @endif
            </div>
            <div class="video">
                <!-- <a href="video.html">
                    <img src="https://img1.gtimg.com/ninja/2/2019/01/ninja154831295447405.png">
                    <p>你绝对不能错过！库里生涯“超劲</p>
                </a>
                <a href="video.html">
                    <img src="https://img1.gtimg.com/ninja/2/2019/01/ninja154831295447405.png">
                    <p>你绝对不能错过！库里生涯“超劲</p>
                </a> -->
                <a class="icon"><img src="{{env('CDN_URL')}}/img/pc/v2/img_home_appdown.jpg"></a>
                @for($i = 1 ; $i < min(3,count($videos)) ; $i++)
                    <a href="{{$videos[$i]['link']}}">
                        <img src="{{$videos[$i]['image']}}">
                        <p>{{$videos[$i]['title']}}</p>
                    </a>
                @endfor
            </div>
        </div>
        <div id="Left_part">
            <div class="el_con">
                <div class="header">
                    <h3><p>最近直播</p></h3>
                    <p class="aline">
                        <a href="/live/">全部比赛直播 ></a>
                    </p>
                </div>
                <table>
                    <col width="40px"><col width="80px"><col width="60px"><col><col width="60px"><col><col width="36%">
                    @for($i = 0 ; $i < min(20,count($matches)); $i++)
                        <?php
                        $match = $matches[$i];
                        $channels = $match['channels'];
                        $sport = $match['sport'];
                        $mid = $match['mid'];
                        $lid = isset($match['lid']) ? $match['lid'] : 0;
                        $url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $mid);
                        $firstChannel = isset($channels[0]) ? $channels[0] : [];
                        ?>
                        <tr type="{{$match['sport'] == 2 ? 'basketball' : 'football'}}">
                            <td><img class="icon" src="{{env('CDN_URL')}}/img/pc/v2/icon_{{$match['sport'] == 2 ? 'basket' : 'foot'}}_light_opaque.png"></td>
                            <td>{{$match['league_name']}}</td>
                            <td>{{date('H:i', strtotime($match['time']))}}</td>
                            @if(isset($match['hid']))
                                <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
                            @else
                                <td>{{$match['hname']}}</td>
                            @endif
                            @if(isset($match['isMatching']) && $match['isMatching'])
                                <td><p class="live">直播中</p></td>
                            @else
                                <td>vs</td>
                            @endif
                            @if(isset($match['aid']))
                                <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
                            @else
                                <td>{{$match['aname']}}</td>
                            @endif
                            <td class="channel">
                                @foreach($channels as $channel)
                                    @continue($channel['platform'] == 5){{-- APP链接过滤 --}}
                                    @if(isset($channel['player']) && $channel['player'] == 16){{-- 外链 --}}
                                    <a target="_blank" rel="nofollow" href="{{$channel['link']}}">{{$channel['name']}}</a>
                                    @else
                                        <?php
                                        if(isset($channel['akq_url']) && strlen($channel['akq_url']) > 0){
                                            $tmp_url = $channel['akq_url'];
                                        }
                                        else{
                                            $tmp_url = $url;
                                        }
                                        ?>
                                        <a target="_blank" href="{{$tmp_url}}">{{$channel['name']}}</a>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endfor
                </table>
                @if(count($endMatches) > 0)
                    <div class="interval"><img src="{{env('CDN_URL')}}/img/pc/v2/icon_my_history.png">已结束比赛</div>
                    <table>
                        <col width="40px"><col width="80px"><col width="60px"><col><col width="90px"><col><col width="140px">
                        @foreach($endMatches as $record)
                            <?php
                            $match = $record['match'];
                            $sport = $match['sport'];
                            $mid = $match['id'];
                            $lid = isset($match['lid']) ? $match['lid'] : 0;
                            $url = $record['link'];
                            ?>
                            <tr type="{{$match['sport'] == 2 ? 'basketball' : 'football'}}">
                                <td><img class="icon" src="{{env('CDN_URL')}}/img/pc/v2/icon_{{$match['sport'] == 2 ? 'basket' : 'foot'}}_light_opaque.png"></td>
                                <td>{{$match['lname']}}</td>
                                <td>{{date('H:i', strtotime($match['time']))}}</td>
                                @if(isset($match['hid']))
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
                                @else
                                    <td>{{$match['hname']}}</td>
                                @endif
                                <td><span class="live">{{$match['hscore']}} - {{$match['ascore']}}</span></td>
                                @if(isset($match['aid']))
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
                                @else
                                    <td>{{$match['aname']}}</td>
                                @endif
                                <td class="channel"><a href="{{$url}}">观看录像</a></td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
        <div id="Right_part">
            <div class="con_box">
                <div class="header_con">
                    <h4>篮球积分排名</h4>
                    <a href="/data/">详细赛事数据</a>
                </div>
                <div class="player_rank">
                    <div class="rank_tab_box">
                        <p class="on" forItem="east">东部</p><p forItem="west">西部</p><p forItem="cba">CBA</p>
                    </div>
                    <table class="east">
                        <col width="15%"><col><col width="24%">
                        @if(isset($scores) && isset($scores['2_1']) && isset($scores['2_1']['east']))
                            @foreach($scores['2_1']['east'] as $score)
                                <tr>
                                    <td class="num">{{$score['rank']}}</td>
                                    <td>
                                        <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,1,$score['tid'])}}">
                                            <img src="{{$score['ticon']}}">
                                            <p class="name">{{$score['tname_short']}}</p>
                                            <p class="info">{{$score['win']}}胜 {{$score['lose']}}负</p>
                                        </a>
                                    </td>
                                    @if($score['win_status'] >= 0)
                                        <td class="score">{{$score['win_status']}}连胜</td>
                                    @else
                                        <td class="score">{{-$score['win_status']}}连败</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    <table class="west" style="display: none;">
                        <col width="15%"><col><col width="24%">
                        @if(isset($scores) && isset($scores['2_1']) && isset($scores['2_1']['west']))
                            @foreach($scores['2_1']['west'] as $score)
                                <tr>
                                    <td class="num">{{$score['rank']}}</td>
                                    <td>
                                        <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,1,$score['tid'])}}">
                                            <img src="{{$score['ticon']}}">
                                            <p class="name">{{$score['tname_short']}}</p>
                                            <p class="info">{{$score['win']}}胜 {{$score['lose']}}负</p>
                                        </a>
                                    </td>
                                    @if($score['win_status'] >= 0)
                                        <td class="score">{{$score['win_status']}}连胜</td>
                                    @else
                                        <td class="score">{{-$score['win_status']}}连败</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    <table class="cba" style="display: none;">
                        <col width="15%"><col><col width="24%">
                        @if(isset($scores) && isset($scores['2_4']['west']))
                            @foreach($scores['2_4']['west'] as $score)
                                <tr>
                                    <td class="num">{{$score['rank']}}</td>
                                    <td>
                                        <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,4,$score['tid'])}}">
                                            <img src="{{$score['ticon']}}">
                                            <p class="name">{{$score['tname_short']}}</p>
                                            <p class="info">{{$score['win']}}胜 {{$score['lose']}}负</p>
                                        </a>
                                    </td>
                                    @if($score['win_status'] >= 0)
                                        <td class="score">{{$score['win_status']}}连胜</td>
                                    @else
                                        <td class="score">{{-$score['win_status']}}连败</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
            <div class="con_box">
                <div class="header_con">
                    <h4>足球积分排名</h4>
                    <a href="/data/">详细赛事数据</a>
                </div>
                <div class="player_rank">
                    <div class="rank_tab_box">
                        <?php $index = 0; ?>
                        @foreach($leagues as $key=>$league)
                            <p @if($index == 0)class="on"@endif forItem="{{$league['name_en']}}">{{$league['name']}}</p>
                            <?php $index++ ?>
                        @endforeach
                    </div>
                    <?php $index = 0; ?>
                    @foreach($leagues as $key=>$league)
                        <?php
                        if (isset($scores[$key])){
                            $tmp = $scores[$key];
                        }
                        ?>
                        <table class="{{$league['name_en']}}" @if($index > 0)style="display: none;"@endif>
                            <col width="15%"><col><col width="24%">
                            @foreach($tmp as $score)
                                <tr>
                                    <td class="num">{{$score['rank']}}</td>
                                    <td>
                                        <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$score['lid'],$score['tid'])}}">
                                            <img src="{{$score['ticon']}}">
                                            <p class="name">{{$score['tname']}}</p>
                                            <p class="info">{{$score['win']}}胜 {{$score['draw']}}平 {{$score['lose']}}负</p>
                                        </a>
                                    </td>
                                    <td class="score">{{$score['score']}}</td>
                                </tr>
                                @endforeach
                        </table>
                        <?php $index++ ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/home_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection
