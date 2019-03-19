@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/live_2.css?time=201901041209">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?time=201901041209">
@endsection
@section("content")
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;>  <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>  >&nbsp;&nbsp;<span class="on">{{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}@endif</span>
            </div>
        </div>
    @else
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;>&nbsp;&nbsp;<span class="on">{{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}@endif</span>
            </div>
        </div>
    @endif
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
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
            <div id="Live_info">
                <div class="team_con">
                    <div class="team">
                        <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match["sport"],$lid,$match['hid'])}}">
                            <img src="{{$host_icon}}"><h2>{{$match['hname']}}</h2>
                        </a>
                    </div>
                    <p class="vs">VS</p>
                    <div class="team">
                        <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match["sport"],$lid,$match['aid'])}}">
                            <img src="{{$away_icon}}"><h2>{{$match['aname']}}</h2>
                        </a>
                    </div>
                </div>
                <div class="line_list">
                    {{--<span>直播线路：</span>--}}
                    <?php $channels = $live['channels']; ?>
                    @if(isset($channels))
                        <?php
                        $btnIndex = 0;
                        $mid = $match["mid"];
                        while (strlen($mid) < 4) {
                            $mid = "0" . $mid;
                        }
                        ?>
                        @foreach($channels as $index=>$channel)
                            @continue($channel['platform'] == \App\Models\Match\MatchLiveChannel::kPlatformApp) {{-- $channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink ||  --}}
                            <?php
                            if ($channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink) {
                                $url = $channel["link"];
                            } else {
                                $url = env('LHB_URL').'/room/' . $match["sport"] . $mid . '.html?btn=' . ($btnIndex++);
                            }
                            ?>
                            <a href="{{$url}}" target="_blank">{{$channel['name']}}</a>
                        @endforeach
                        @if($adShow)
                            <a href="{{$adUrl}}" target="_blank" style="border-color: #d24545; background: #d24545; color: #fff;">{{$adName}}</a>
                        @endif
                        <a href="https://xinhi.com/live_detail/{{$match['sport']}}_{{$mid}}.html" target="_blank" >备用信号</a>
                    @endif
                </div>
            </div>
            {{--<div class="iframe" id="Video">--}}
            {{--<!-- <iframe id="Frame" src="player.html?id=123"></iframe> -->--}}
            {{--</div>--}}
            {{--<div class="share" id="Share">--}}
            {{--复制此地址分享：<input type="text" name="share" value="{{$ma_url}}" onclick="Copy()"><span></span>--}}
            {{--</div>--}}
            @if((isset($hRecords) && count($hRecords) > 0) || (isset($aRecords) && count($aRecords) > 0))
                <?php
                $hteamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid']);
                $ateamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid']);
                ?>
                <div class="el_con">
                    <div class="header">
                        <h3><p>两队最近比赛录像</p></h3>
                        <p class="aline">
                            <a href="{{isset($zhuanti) ? '/'.$zhuanti['name_en']:''}}/record/">更多{{$lname}}录像 ></a>
                        </p>
                    </div>
                    @if(isset($hRecords) && count($hRecords) > 0)
                        <table class="match">
                            <col width="8.5%"><col width="19%"><col><col width="15%"><col><col width="15%">
                            <tr>
                                <th colspan="6"><img src="{{$host_icon}}"><h4><a href="{{$hteamUrl}}">{{$match['hname']}}</a></h4></th>
                            </tr>
                            @foreach($hRecords as $record)
                                <?php
                                $time = date('Y-m-d H:m', date_create($record['time'])->getTimestamp());
                                $r_lid = $record['s_lid'];
                                if (!is_null($record->url)){
                                    $url = $record->url;
                                }
                                else{
                                    $url = '/record/';
                                }
                                $r_hteamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($record['sport'], $r_lid, $record['hid']);
                                $r_ateamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($record['sport'], $r_lid, $record['aid']);
                                ?>
                                <tr>
                                    <td><span>{{$record['lname']}}</span></td>
                                    <td><span>{{$time}}</span></td>
                                    <td class="host"><a href="{{$r_hteamUrl}}">{{$record['hname']}}</a></td>
                                    <td class="vs">{{$record['hscore']}} - {{$record['ascore']}}</td>
                                    <td class="away"><a href="{{$r_ateamUrl}}">{{$record['aname']}}</a></td>
                                    <td><a href="{{$url}}" class="record">[比赛录像]</a></td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                    @if(isset($aRecords) && count($aRecords) > 0)
                        <table class="match">
                            <col width="8.5%"><col width="19%"><col><col width="15%"><col><col width="15%">
                            <tr>
                                <th colspan="6"><img src="{{$away_icon}}"><h4><a href="{{$ateamUrl}}">{{$match['aname']}}</a></h4></th>
                            </tr>
                            @foreach($aRecords as $record)
                                <?php
                                $time = date('Y-m-d H:m', date_create($record['time'])->getTimestamp());
                                $r_lid = $record['s_lid'];
                                if (!is_null($record->url)){
                                    $url = $record->url;
                                }
                                else{
                                    $url = '/record/';
                                }
                                $r_hteamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($record['sport'], $r_lid, $record['hid']);
                                $r_ateamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($record['sport'], $r_lid, $record['aid']);
                                ?>
                                <tr>
                                    <td><span>{{$record['lname']}}</span></td>
                                    <td><span>{{$time}}</span></td>
                                    <td class="host"><a href="{{$r_hteamUrl}}">{{$record['hname']}}</a></td>
                                    <td class="vs">{{$record['hscore']}} - {{$record['ascore']}}</td>
                                    <td class="away"><a href="{{$r_ateamUrl}}">{{$record['aname']}}</a></td>
                                    <td><a href="{{$url}}" class="record">[比赛录像]</a></td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
            @endif
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
                            <col width="16%"><col width="23%"><col><col width="14%"><col>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                            </tr>
                            <tbody>
                            @foreach($passVSMatches as $pMatch)
                                <?php $fv = \App\Models\Subject\SubjectVideo::firstVideo($pMatch['id']); ?>
                                <tr>
                                    <td>{{$pMatch->getLeagueName()}}</td>
                                    <td>{{substr($pMatch['time'], 2, 14)}}</td>
                                    <td><a {{$pMatch['hid']}} target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pMatch['lid'], $pMatch['hid'])}}">{{$pMatch['hname']}}</a></td>
                                    <td>{{$pMatch['hscore']}}-{{$pMatch['ascore']}}</td>
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pMatch['lid'], $pMatch['aid'])}}">{{$pMatch['aname']}}</a></td>
                                    {{--<td>@if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">录像</a>@endif</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                    @if(isset($hNearMatches) && count($hNearMatches) > 0)
                        <p class="title">{{$match['hname']}}近期战绩</p>
                        <table>
                            <col width="16%"><col width="23%"><col><col width="14%"><col>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                            </tr>
                            <tbody>
                            @foreach($hNearMatches as $hMatch)
                                <?php $fv = \App\Models\Subject\SubjectVideo::firstVideo($hMatch['id']); ?>
                                <tr>
                                    <td>{{$hMatch->getLeagueName()}}</td>
                                    <td>{{substr($hMatch['time'], 2, 14)}}</td>
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $hMatch['lid'], $hMatch['hid'])}}">{{$hMatch['hname']}}</a></td>
                                    <td>{{$hMatch['hscore']}}-{{$hMatch['ascore']}}</td>
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $hMatch['lid'], $hMatch['aid'])}}">{{$hMatch['aname']}}</a></td>
                                    {{--<td>@if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">录像</a>@endif</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                    @if(isset($aNearMatches) && count($aNearMatches) > 0)
                        <p class="title">{{$match['aname']}}近期战绩</p>
                        <table>
                            <col width="16%"><col width="23%"><col><col width="14%"><col>
                            <tr>
                                <th>赛事</th>
                                <th>时间</th>
                                <th>主队</th>
                                <th>比分</th>
                                <th>客队</th>
                            </tr>
                            <tbody>
                            @foreach($aNearMatches as $aMatch)
                                <?php $fv = \App\Models\Subject\SubjectVideo::firstVideo($aMatch['id']); ?>
                                <tr>
                                    <td>{{$aMatch->getLeagueName()}}</td>
                                    <td>{{substr($aMatch['time'], 2, 14)}}</td>
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $aMatch['lid'], $aMatch['hid'])}}">{{$aMatch['hname']}}</a></td>
                                    <td>{{$aMatch['hscore']}}-{{$aMatch['ascore']}}</td>
                                    <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $aMatch['lid'], $aMatch['aid'])}}">{{$aMatch['aname']}}</a></td>
                                    {{--<td>@if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">录像</a>@endif</td>--}}
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
            <div class="el_con more_live_con">
                <div class="header">
                    <h3><p>更多直播</p></h3>
                    <p class="aline">
                        <a href="/">全部直播 ></a>
                    </p>
                </div>
                <table class="match">
                    <col width="8%"><col width="12%"><col width="9%"><col><col width="9%"><col><col width="30%">
                    @foreach($moreLives as $more)
                        <?php
                        $mChannels = $more['channels'];
                        $url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($more['sport'], $more['lid'], $more['mid']);
                        $mIndex = 0;
                        $hteamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($more['sport'], $more['lid'], $more['hid']);
                        $ateamUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($more['sport'], $more['lid'], $more['aid']);
                        ?>
                        <tr>
                            <td>
                                @if($more['sport'] == 2)
                                    <img src="{{env('CDN_URL')}}/img/pc/v2/icon_basket_light_opaque.png" class="type">
                                @else
                                    <img src="{{env('CDN_URL')}}/img/pc/v2/icon_foot_light_opaque.png" class="type">
                                @endif
                            </td>
                            <td><span>{{$more['league_name']}}</span></td>
                            <td><span>{{substr($more['time'], 5, 5)}}<br/>{{substr($more['time'], 11, 5)}}</span></td>
                            <td class="host"><a href="{{$hteamUrl}}">{{$more['hname']}}</a></td>
                            <td class="vs">vs</td>
                            <td class="away"><a href="{{$ateamUrl}}">{{$more['aname']}}</a></td>
                            <td class="line">
                                @foreach($mChannels as $mch)
                                    @continue(isset($mch['platform']) && $mch['platform'] == 5)
                                    @if(isset($mch['player']) && $mch['player'] == 16){{-- 外链 --}}
                                        <a class="live" target="_blank" href="{{$mch["link"]}}">{{$mch['name']}}</a>
                                    @else
                                        <?php
                                        if(isset($mch['akq_url']) && strlen($mch['akq_url']) > 0){
                                            $tmp_url = $mch['akq_url'];
                                        }
                                        else{
                                            $tmp_url = $url;
                                        }
                                        ?>
                                        <a class="live" target="_blank" href="{{$tmp_url . '#btn=' . ($mIndex++)}}">{{$mch['name']}}</a>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div id="Right_part">
            <div class="con_box">
                <div class="header_con">
                    <h4>{{isset($zhuanti) ? $zhuanti['name'] : '最新'}}赛事直播</h4>
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
            <?php
            $sTitle = isset($zhuanti) ? $zhuanti['name'] : '';
            ?>
            <div class="con_box">
                <div class="header_con">
                    <h4>{{strlen($sTitle) == 0 ? '最新' : $sTitle}}资讯</h4>
                    <a href="{{strlen($sTitle) == 0 ? '/news/':'/'.$zhuanti['name_en'].'/news/'}}">全部{{$sTitle}}资讯</a>
                </div>
                <div class="news">
                    @if(isset($comboData) && isset($comboData['articles']))
                        @foreach($comboData['articles'] as $index=>$article)
                            @if($index < 2)
                                <a href="{{$article['link']}}" class="img_news">
                                    <p class="img_box"><img src="{{$article['cover']}}"></p>
                                    <h3>{{$article['title']}}</h3>
                                </a>
                            @else
                                <a href="{{$article['link']}}" class="text_new"><h4>{{$article['title']}}</h4></a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="con_box">
                <div class="header_con">
                    <h4>最新{{$sTitle}}视频</h4>
                    <a href="{{!isset($zhuanti) ? '/video/':'/'.$zhuanti['name_en'].'/video/'}}">{{$sTitle}}视频集锦</a>
                    <a href="{{!isset($zhuanti) ? '/record/':'/'.$zhuanti['name_en'].'/record/'}}">{{$sTitle}}比赛录像</a>
                </div>
                <div class="video">
                    @if(isset($comboData) && isset($comboData['videos']))
                        @foreach($comboData['videos'] as $video)
                            <div class="video_item">
                                <a href="{{$video['link']}}">
                                    <p class="img_box"><img src="{{$video['image']}}"></p>
                                    <p class="text_box">{{$video['title']}}</p>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
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
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/live_2.js?201903111814"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/detail_self.js?time=201901181530"></script>
    <script type="text/javascript">
        var LeagueKeyword = '{{isset($zhuanti) ? $zhuanti['name_en']:'all'}}';
        window.onload = function () { //需要添加的监控放在这里
//            setADClose();
            setPage();
        }
        window.LHB_URL = "{{env("LHB_URL", "https://lehubo.com")}}";
        @if($adShow)
        //initLineChannel("{{env('API_URL')}}/json/pc/channels/{{$sport}}/{{$match['mid']}}.json?time="+(new Date()).getTime(), "{{$match["mid"]}}", "{{$match["sport"]}}", "{{$adName}}", "{{$adUrl}}");
        @else
        //initLineChannel("{{env('API_URL')}}/json/pc/channels/{{$sport}}/{{$match['mid']}}.json?time="+(new Date()).getTime(), "{{$match["mid"]}}", "{{$match["sport"]}}");
        @endif
    </script>
@endsection