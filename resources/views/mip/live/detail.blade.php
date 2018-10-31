<?php
$keywords = "爱看球," . $match['lname'] ."直播," . $match['hname'] . "," . $match['aname'];
$description = "《" . $match['hname'] . ' VS ' . $match['aname'] . "》高清全场回放";
?>
@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/videoPhone2.css">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>JRS低调看爱看球直播_{{$match['hname']}}VS{{$match['aname']}}</h1>
        <div class="banner"><a class="home" href="{{\App\Http\Controllers\Mip\UrlCommonTool::homeLivesUrl()}}"></a>{{$match['hname']}}VS{{$match['aname']}}</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Video">
        <mip-iframe layout="fixed-height" width="100" height="210" allowfullscreen allowtransparency="true" src="{{env('WWW_URL')}}/live/spPlayer/player-{{$match['mid']}}-{{$match['sport']}}.html" id="MyIframe">
        </mip-iframe>
    </div>
    <div id="Content">
        <mip-vd-tabs class="tab">
            <section>
                <li>数据</li>
                <li>阵容</li>
                @if($hasEvents)<li>事件</li>@endif
                @if($hasArticle)<li>新闻</li>@endif
                @if($hasVideo)<li>录像</li>@endif
            </section>
            <div id="Data">
                @if(isset($passVSMatches) && count($passVSMatches) > 0)
                <div class="default">
                    <p class="title">对赛往绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>对阵</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($passVSMatches as $pm)
                        <?php
                            $pDate = date('Y/m/d H:i', strtotime($pm['time']));
                            $fv = \App\Models\Subject\SubjectVideo::firstVideo($pm['id']);
                        ?>
                        <tr>
                            <td>{{$pm['win_lname']}}</td>
                            <td><span>{{substr($pDate, 2 , 8)}}</span><br/>{{substr($pDate, 10, 6)}}</td>
                            <td>
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pm['lid'], $pm['hid'])}}">{{$pm['hname']}}</a>
                                {{$pm['hscore']}} - {{$pm['ascore']}}
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pm['lid'], $pm['aid'])}}">{{$pm['aname']}}</a>
                            </td>
                            <td>
                                @if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">回看</a>@endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                @if(isset($hNearMatches) && count($hNearMatches) > 0)
                <div class="default">
                    <p class="title">{{$match['hname']}}近期战绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>对阵</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hNearMatches as $hm)
                        <?php
                            $pDate = date('Y/m/d H:i', strtotime($hm['time']));
                            $fv = \App\Models\Subject\SubjectVideo::firstVideo($hm['id']);
                        ?>
                        <tr>
                            <td>{{$hm['win_lname']}}</td>
                            <td><span>{{substr($pDate, 2 , 8)}}</span><br/>{{substr($pDate, 10, 6)}}</td>
                            <td>
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $hm['lid'], $hm['hid'])}}">{{$hm['hname']}}</a>
                                {{$hm['hscore']}} - {{$hm['ascore']}}
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $hm['lid'], $hm['aid'])}}">{{$hm['aname']}}</a>
                            </td>
                            <td>
                                @if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">回看</a>@endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                @if(isset($aNearMatches) && count($aNearMatches) > 0)
                <div class="default">
                    <p class="title">{{$match['aname']}}近期战绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>对阵</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($aNearMatches as $am)
                        <?php
                            $pDate = date('Y/m/d H:i', strtotime($am['time']));
                            $fv = \App\Models\Subject\SubjectVideo::firstVideo($am['id']);
                        ?>
                        <tr>
                            <td>{{$am['win_lname']}}</td>
                            <td><span>{{substr($pDate, 2 , 8)}}</span><br/>{{substr($pDate, 10, 6)}}</td>
                            <td>
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $am['lid'], $am['hid'])}}">{{$am['hname']}}</a>
                                {{$am['hscore']}} - {{$am['ascore']}}
                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $am['lid'], $am['aid'])}}">{{$am['aname']}}</a>
                            </td>
                            <td>
                                @if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">回看</a>@endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            <div id="Player">
                <div class="host default">
                    <p class="title">{{$match['hname']}}</p>
                    <table>
                        <thead>
                        <tr>
                            <th>号码</th>
                            <th>姓名</th>
                            {{--<th>位置</th>--}}
                            <th>首发</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lineup['home']))
                            @foreach($lineup['home'] as $hl)
                                <tr>
                                    <td><p>{{$hl['num']}}</p></td>
                                    <td>{{$hl['name']}}</td>
                                    {{--<td>门将</td>--}}
                                    <td>{{$hl['first'] == 1 ? '是' : '否'}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="away default">
                    <p class="title">{{$match['aname']}}</p>
                    <table>
                        <thead>
                        <tr>
                            <th>号码</th>
                            <th>姓名</th>
                            {{--<th>位置</th>--}}
                            <th>首发</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lineup['away']))
                            @foreach($lineup['away'] as $al)
                                <tr>
                                    <td><p>{{$al['num']}}</p></td>
                                    <td>{{$al['name']}}</td>
                                    {{--<td>门将</td>--}}
                                    <td>{{$al['first'] == 1 ? '是' : '否'}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if($hasEvents)
            <div id="Technology">
                <div class="event default">
                    <p class="title">详细事件</p>
                    <table>
                        <thead>
                        <tr>
                            <th>{{$match['hname']}}</th>
                            <th>事件</th>
                            <th>时间</th>
                            <th>事件</th>
                            <th>{{$match['aname']}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td>@if($event['is_home']){!! \App\Models\LgMatch\MatchEvent::getEventCnByWap($event['kind'], $event['player_name_j'], $event['player_name_j2'])!!}@endif</td>
                                <td>@if($event['is_home']){{\App\Models\LgMatch\MatchEvent::getKindCn($event['kind'])}}@endif</td>
                                <td>{{$event['happen_time']}}'</td>
                                <td>@if(!$event['is_home']){{\App\Models\LgMatch\MatchEvent::getKindCn($event['kind'])}}@endif</td>
                                <td>@if(!$event['is_home']){!! \App\Models\LgMatch\MatchEvent::getEventCnByWap($event['kind'], $event['player_name_j'], $event['player_name_j2'])!!}@endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if($hasArticle)
            <div id="News">
                @foreach($articles as $article)
                <a href="{{$article->url}}">
                    <mip-img height="80" width="120" layout="responsive" src="{{$article['cover']}}"></mip-img>
                    <p class="con">{{$article['title']}}</p>
                </a>
                @endforeach
            </div>
            @endif
            @if($hasVideo)
            <div id="Record">
                @foreach($videos as $video)
                    <div class="item">
                        <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $video['id'], 'video')}}">
                            <mip-img height="100" layout="responsive" src="{{empty($video['cover']) ? env('CDN_URL').'/img/pc/video_bg.jpg' : $video['cover']}}"></mip-img>
                            <p class="con">{{$video['title']}}</p>
                        </a>
                    </div>
                @endforeach
            </div>
            @endif
        </mip-vd-tabs>
    </div>
@endsection
@section("js")
    <script src="https://c.mipcdn.com/static/v1/mip-vd-tabs/mip-vd-tabs.js"></script>
@endsection