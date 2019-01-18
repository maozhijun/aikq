@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/video.css?time=201901041209">
    <style>
        p.line button a {

        }
    </style>
@endsection
@section("content")
<div id="Content">
    <div class="inner">
        @if(isset($zhuanti))
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;>  <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>  >&nbsp;&nbsp;<span class="on">{{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}@endif</span></div>
        @else
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;>&nbsp;&nbsp;<span class="on">{{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}@endif</span></div>
        @endif
        <div class="right_part">
            @if(isset($leagueLives) && count($leagueLives) > 0)
            <div id="League">
                <p class="title">{{$match['win_lname']}}赛事直播</p>
                <ul>
                    @foreach($leagueLives as $leagueLive)
                    <li>
                        <p class="time">{{date('m/d H:i', strtotime($leagueLive['time']))}}</p>
                        <p class="status">
                            {{--@if($leagueLive->status == -1)已结束--}}
                            {{--@elseif($leagueLive->status > 0 && \App\Models\Match\MatchLive::isLive($leagueLive['id'], $sport, \App\Models\Match\MatchLiveChannel::kPlatformPC))--}}
                                {{--<a class="live" target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $leagueLive['id'])}}">直播中</a>--}}
                            {{--@endif--}}
                        </p>
{{--                        @if($leagueLive->status > 0 && \App\Models\Match\MatchLive::isLive($leagueLive['id'], $sport, \App\Models\Match\MatchLiveChannel::kPlatformPC))--}}
                            <p class="team"><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $leagueLive['id'])}}">{{$leagueLive['hname']}} VS {{$leagueLive['aname']}}</a></p>
                        {{--@else--}}
                            {{--<p class="team">{{$leagueLive['hname']}} VS {{$leagueLive['aname']}}</p>--}}
                        {{--@endif--}}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if(isset($articles) && count($articles) > 0)
            <div id="News">
                <p class="title">相关新闻</p>
                @foreach($articles as $index=>$article)
                    @if($index < 3)
                        <a class="big" target="_blank" href="{{$article->url}}">
                            @if(!empty($article->cover))<p class="imgbox" style="background: url({{$article->cover}}); background-size: cover;"></p>@endif
                            <p class="con">{{$article->title}}</p>
                        </a>
                    @else
                        <a target="_blank" class="small" href="{{$article->url}}">{{$article->title}}</a>
                    @endif
                @endforeach
            </div>
            @endif
            @if(isset($videos) && count($videos) )
            <div id="Record">
                <p class="title">相关录像</p>
                @foreach($videos as $video)
                <?php $vTitle = $video->getVideoTitle(); ?>
                <a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $video['id'], 'video')}}" title="{{$vTitle}}">
                    <p class="imgbox" style="background: url({{empty($video['cover']) ? env('CDN_URL').'/img/pc/akq_pc_default_n.jpg' : $video['cover']}}); background-size: cover;"></p>
                    <p class="name">{{$vTitle}}</p>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        <div class="left_part">
            <?php
                $adShow = env("TOUZHU_AD", "false") == "true";
                if (isset($match["lname"]) && !empty($match["lname"])) {
                    $lname = $match["lname"];
                } else if (isset($match["win_lname"]) && !empty($match["win_lname"])) {
                    $lname = $match["win_lname"];
                } else {
                    $lname = "体育";
                }
                $lname = mb_strlen($lname) > 3 ? "体育" : $lname;
                $adName = $lname."投注";
                $adUrl = "http://b.aikq.cc/b8888.html";
            ?>
                {{--<div class="adbanner inner"><a href="http://b.aikq.cc/b8888.html" target="_blank"><img src="{{env("CDN_URL")}}/img/pc/room.gif"><button class="close"></button></a></div>--}}
                <div id="Info">
                <h1 class="name">{{$match['lname']}}直播：{{$match['hname']}}@if(!empty($match['aname']))　VS　{{$match['aname']}}@endif</h1>
                <div class="match">
                    <div class="team">
                        <img src="{{$host_icon}}" onerror="this.src='{{env('CDN_URL').'/img/pc/icon_teamDefault.png'}}'">
                        <p>{{$match['hname']}}</p>
                    </div>
                    <div class="info">
                        <p>{{$match['win_lname']}}</p>
                        <p>{{substr($match['time'], 0, 16)}}</p>
                    </div>
                    <div class="team">
                        <img src="{{$away_icon}}" onerror="this.src='{{env('CDN_URL').'/img/pc/icon_teamDefault.png'}}'">
                        <p>{{$match['aname']}}</p>
                    </div>
                </div>
                <p class="line">
                    {{--<span>直播线路：</span>--}}
                    <?php $channels = $live['channels']; ?>
                    @if(isset($channels))
                        @foreach($channels as $index=>$channel)
                            @continue($channel['platform'] == \App\Models\Match\MatchLiveChannel::kPlatformApp) {{-- $channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink ||  --}}
                            <?php
                                if ($channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink) {
                                    $url = $channel["link"];
                                } else {
                                    $url = env('LHB_URL').'/live/spPlayer/player-' . $match["mid"] . '-' . $match["sport"] . '.html';
                                }
                            ?>
                            <a href="{{$url}}" target="_blank">{{$channel['name']}}</a>
                            {{--<button id="{{$channel['channelId']}}"onclick="ChangeChannel('{{$link}}', this)">{{$channel['name']}}</button>--}}
                        @endforeach
                        @if($adShow)
                        <a href="{{$adUrl}}" target="_blank" style="border-color: #d24545; background: #d24545; color: #fff;">{{$adName}}</a>
                        <a href="https://xinhi.com/live_detail/{{$match['sport']}}_{{$match['mid']}}.html" target="_blank" >备用信号</a>
                        @endif
                    @endif
                </p>
            </div>
            {{--<div class="iframe" id="Video">--}}
                {{--<!-- <iframe id="Frame" src="player.html?id=123"></iframe> -->--}}
            {{--</div>--}}
            {{--<div class="share" id="Share">--}}
                {{--复制此地址分享：<input type="text" name="share" value="{{$ma_url}}" onclick="Copy()"><span></span>--}}
            {{--</div>--}}
            @if($adShow)<div class="adbanner inner"><a href="http://b.aikq.cc/b8888.html" target="_blank"><img src="{{env("CDN_URL")}}/img/pc/room.gif"><button class="close"></button></a></div>@endif
            <div id="Data">
                <div class="column">
                    <a href="javascript:void(0)" class="on" value="Analysis">数据分析</a>
                    @if($hasLineup)<a href="javascript:void(0)" value="Lineup">球队阵容</a>@endif
                    @if($hasTech)<a href="javascript:void(0)" value="Technology">技术统计</a> @endif
                </div>
                <div id="Analysis" style="display: ;">
                    @if(isset($passVSMatches) && count($passVSMatches) > 0)
                    <p class="title">对往赛事</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($passVSMatches as $pMatch)
                        <?php $fv = \App\Models\Subject\SubjectVideo::firstVideo($pMatch['id']); ?>
                        <tr>
                            <td>{{$pMatch->getLeagueName()}}</td>
                            <td>{{substr($pMatch['time'], 2, 14)}}</td>
                            <td><a {{$pMatch['hid']}} target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pMatch['lid'], $pMatch['hid'])}}">{{$pMatch['hname']}}</a></td>
                            <td>{{$pMatch['hscore']}}-{{$pMatch['ascore']}}</td>
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pMatch['lid'], $pMatch['aid'])}}">{{$pMatch['aname']}}</a></td>
                            <td>@if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">录像</a>@endif</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($hNearMatches) && count($hNearMatches) > 0)
                    <p class="title">{{$match['hname']}}近期战绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hNearMatches as $hMatch)
                        <?php $fv = \App\Models\Subject\SubjectVideo::firstVideo($hMatch['id']); ?>
                        <tr>
                            <td>{{$hMatch->getLeagueName()}}</td>
                            <td>{{substr($hMatch['time'], 2, 14)}}</td>
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $hMatch['lid'], $hMatch['hid'])}}">{{$hMatch['hname']}}</a></td>
                            <td>{{$hMatch['hscore']}}-{{$hMatch['ascore']}}</td>
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $hMatch['lid'], $hMatch['aid'])}}">{{$hMatch['aname']}}</a></td>
                            <td>@if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">录像</a>@endif</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($aNearMatches) && count($aNearMatches) > 0)
                    <p class="title">{{$match['aname']}}近期战绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($aNearMatches as $aMatch)
                        <?php $fv = \App\Models\Subject\SubjectVideo::firstVideo($aMatch['id']); ?>
                        <tr>
                            <td>{{$aMatch->getLeagueName()}}</td>
                            <td>{{substr($aMatch['time'], 2, 14)}}</td>
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $aMatch['lid'], $aMatch['hid'])}}">{{$aMatch['hname']}}</a></td>
                            <td>{{$aMatch['hscore']}}-{{$aMatch['ascore']}}</td>
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $aMatch['lid'], $aMatch['aid'])}}">{{$aMatch['aname']}}</a></td>
                            <td>@if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">录像</a>@endif</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="Lineup" style="display: none;">
                    <div class="team">
                        @if($hasLineup)
                        <p class="title">{{$match['hname']}}</p>
                        <table>
                            <thead>
                            <tr>
                                <th>{{$match['sport'] == 1 ? '号码' : '位置'}}</th>
                                <th>姓名</th>
                                <th>首发</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($lineup['home']))
                            @foreach($lineup['home'] as $l)
                            <tr>
                                <td>{{$match['sport'] == 1 ? $l['num'] : $l['location']}}</td>
                                <td>{{$l['name']}}</td>
                                <td>{{$l['first'] == 1 ? '是' :'否'}}</td>
                            </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                        @endif
                    </div>
                    <div class="team">
                        <p class="title">{{$match['aname']}}</p>
                        <table>
                            <thead>
                            <tr>
                                <th>{{$match['sport'] == 1 ? '号码' : '位置'}}</th>
                                <th>姓名</th>
                                <th>首发</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($lineup['away']))
                                @foreach($lineup['away'] as $l)
                                    <tr>
                                        <td>{{$match['sport'] == 1 ? $l['num'] : $l['location']}}</td>
                                        <td>{{$l['name']}}</td>
                                        <td>{{$l['first'] == 1 ? '是' :'否'}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="Technology" style="display: none;">
                    @if(isset($tech) && count($tech))
                    <p class="title">本次技术统计</p>
                    <table>
                        <colgroup>
                            <col>
                            <col width="{{$sport == 2 ? 100 : 45}}">
                            <col width="15%">
                            <col width="{{$sport == 2 ? 100 : 45}}">
                            <col>
                        </colgroup>
                        @foreach($tech as $t)
                        <tr>
                            <td>
                                <p><span style="width: {{ (isset($t['h_p']) && is_numeric($t['h_p'])) ? ($t['h_p'] * 100) : 0 }}%;"></span></p>
                            </td>
                            <td>{{$t['h']}}</td>
                            <td>{{$t['name']}}</td>
                            <td>{{$t['a']}}</td>
                            <td>
                                <p><span style="width: {{ (isset($t['a_p']) && is_numeric($t['a_p'])) ? ($t['a_p'] * 100) : 0 }}%;"></span></p>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    @endif
                    @if(isset($events) && count($events) > 0)
                    <p class="title">详细事件</p>
                    <table>
                        <colgroup>
                            <col>
                            <col width="16%">
                            <col width="11%">
                            <col width="16%">
                            <col>
                        </colgroup>
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
                            <td>@if($event['is_home']){{\App\Models\LgMatch\MatchEvent::getKindCn($event['kind'])}}@endif</td>
                            <td>@if($event['is_home']){{\App\Models\LgMatch\MatchEvent::getEventCn($event['kind'], $event['player_name_j'], $event['player_name_j2'])}}@endif</td>
                            <td>{{$event['happen_time']}}‘</td>
                            <td>@if(!$event['is_home']){{\App\Models\LgMatch\MatchEvent::getKindCn($event['kind'])}}@endif</td>
                            <td>@if(!$event['is_home']){{\App\Models\LgMatch\MatchEvent::getEventCn($event['kind'], $event['player_name_j'], $event['player_name_j2'])}}@endif</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            @if(count($moreLives) > 0)
            <div id="Other">
                <p class="title">更多直播</p>
                <table>
                    <colgroup>
                        <col width="120">
                        <col width="100">
                        <col width="30%">
                        <col>
                        <col width="100">
                    </colgroup>
                    @foreach($moreLives as $more)
                        <?php
                        $mChannels = $more['channels'];
                        $url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($more['sport'], $more['lid'], $more['mid']);
                        $mIndex = 0;
                        ?>
                    <tr>
                        <td>{{$more['league_name']}}</td>
                        <td>{{substr($more['time'], 5, 11)}}</td>
                        <td><a target="_blank" href="{{$url}}">{{$more['hname']}}&nbsp;VS&nbsp;{{$more['aname']}}</a></td>
                        <td>
                            @foreach($mChannels as $mch)
                                @continue(isset($mch['platform']) && $mch['platform'] == 5)
                                @if(isset($mch['player']) && $mch['player'] == 16){{-- 外链 --}}
                                <a target="_blank" href="/live/ex-link/{{$mch['id']}}">{{$mch['name']}}</a>
                                @else
                                    <?php
                                    if(isset($mch['akq_url']) && strlen($mch['akq_url']) > 0){
                                        $tmp_url = $mch['akq_url'];
                                    }
                                    else{
                                        $tmp_url = $url;
                                    }
                                    ?>
                                    <a target="_blank" href="{{$tmp_url . '#btn=' . ($mIndex++)}}">{{$mch['name']}}</a>
                                @endif
                            @endforeach
                        </td>
                        <td></td>{{-- @if($more['isMatching'])<a target="_blank" href="{{$url}}">直播中</a>@endif --}}
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
        </div>
    </div>
    <div class="clear"></div>
</div>
<!-- <div class="adflag left">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div>
<div class="adflag right">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div> -->
@endsection
@section("js")
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/video.js"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/detail_self.js?time=201901181530"></script>
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        setADClose();
        setPage();
    }
    window.LHB_URL = "{{env("LHB_URL", "https://lehubo.com")}}";
    @if($adShow)
    initLineChannel("{{env('API_URL')}}/json/pc/channels/{{$sport}}/{{$match['mid']}}.json?time="+(new Date()).getTime(), "{{$match["mid"]}}", "{{$match["sport"]}}", "{{$adName}}", "{{$adUrl}}");
    @else
    initLineChannel("{{env('API_URL')}}/json/pc/channels/{{$sport}}/{{$match['mid']}}.json?time="+(new Date()).getTime(), "{{$match["mid"]}}", "{{$match["sport"]}}");
    @endif
</script>
@endsection