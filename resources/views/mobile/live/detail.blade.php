@extends('mobile.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoPhone.css?time=201901181541">
@endsection
<?php
    $channels = isset($live) ? $live['channels'] : [];
    if (isset($channels) && count($channels) > 0) {
        $btnIndex = request('btn', 0);
        $firstCh = isset($channels[$btnIndex]) ? $channels[$btnIndex] : $channels[0];
    }
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
?>
@section("banner")
    <div id="Navigation">
        @if(isset($h1))<h1>{{$h1}}</h1>@endif
        <div class="banner"><a class="home" href="/"></a>{{$match['win_lname']}}比赛直播</div>
        @if(isset($channels))
        <div class="select" style="display: none;">{{isset($firstCh) ? $firstCh['name'] : '线路一'}}</div>
        <select style="display: none;">
            @foreach($channels as $channel)
                <?php
                $content = $channel['link'];
                $player = $channel['player'];
                if ($player == 11) {
                    $link = '/live/iframe/player-'.$channel['id'].'-'.$channel['type'].'.html';
                } else {
                    $link = '/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
                }
                $ex = $channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink;
                if ($ex) {
                    $url = $content;
                } else {
                    $url = env('LHB_URL') . $link;
                }
                $ex = 1;
                ?>
            <option ex="{{$ex}}" value="{{$url}}">{{$channel['name']}}</option>
            @endforeach
            @if($adShow)<option ex="1" value="http://b.aikq.cc/b8888.html" style="background: #d24545">{{$adName}}</option>@endif
            <option ex="1" value="https://xinhi.com/live_detail/{{$match['sport']}}_{{$match['mid']}}.html" style="background: #d24545">备用信号</option>
        </select>
        @endif
    </div>
    <div class="default" id="Video">
        <iframe src="" id="MyIframe"></iframe>
        <div id="Unload">
            <div class="team">
                <img src="{{$host_icon or ''}}" onerror="this.src='{{env('CDN_URL').'/img/pc/icon_teamDefault.png'}}'">
                <p>{{$match['hname']}}</p>
            </div>
            <div class="info">
                <p class="league">{{$match['win_lname']}}</p>
                <p class="time">{{substr($match['time'], 0, 11)}}<br/>{{substr($match['time'], 11, 5)}}</p>
                <button>视频直播</button>
                <select>
                    <option value="">请选择线路</option>
                    <?php $btnIndex = 0; ?>
                    @foreach($channels as $channel)
                        <?php
                        $content = $channel['link'];
                        $player = $channel['player'];
                        $ex = $channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink;
                        if ($ex) {
                            $url = $content;
                        } else {
                            $url = env('LHB_URL') . '/room/' . $match["sport"] . $match["mid"] . '.html?btn='.($btnIndex++);
                        }
                        $ex = 1;
                        ?>
                        <option ex="{{$ex}}" value="{{$url}}">{{$channel['name']}}</option>
                    @endforeach
                    @if($adShow)<option ex="1" value="http://b.aikq.cc/b8888.html" style="background: #d24545">{{$adName}}</option>@endif
                    <option ex="1" value="https://xinhi.com/live_detail/{{$match['sport']}}_{{$match['mid']}}.html">备用信号</option>
                </select>
            </div>
            <div class="team">
                <img src="{{$away_icon or ''}}" onerror="this.src='{{env('CDN_URL').'/img/pc/icon_teamDefault.png'}}'">
                <p>{{$match['aname']}}</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="Content">
        <div class="tabbox">
            <button class="on" value="Data">数据分析</button>
            <button value="Player">球队阵容</button>
            <button value="Technology">技术统计</button>
            @if($hasArticle)<button value="News">相关新闻</button>@endif
            @if($hasVideos)<button value="Record">相关录像</button>@endif
        </div>
        <div id="Data" style="display: ;">
            @if(isset($passVSMatches) && count($passVSMatches) > 0)
            <div class="default">
                <p class="title">对赛往绩</p>
                <table>
                    <colgroup>
                        <col width="120px">
                        <col width="130px">
                        <col>
                        <col width="140px">
                        <col>
                        <col width="90px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>赛事</th>
                        <th>时间</th>
                        <th colspan="3">对阵</th>
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
                        </td>
                        <td>{{$pm['hscore']}} - {{$pm['ascore']}}</td>
                        <td>
                            <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $pm['lid'], $pm['hid'])}}">{{$pm['aname']}}</a>
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
                    <colgroup>
                        <col width="120px">
                        <col width="130px">
                        <col>
                        <col width="140px">
                        <col>
                        <col width="90px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>赛事</th>
                        <th>时间</th>
                        <th  colspan="3">对阵</th>
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
                        </td>
                        <td>{{$hm['hscore']}} - {{$hm['ascore']}}</td>
                        <td>
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
                    <colgroup>
                        <col width="120px">
                        <col width="130px">
                        <col>
                        <col width="140px">
                        <col>
                        <col width="90px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>赛事</th>
                        <th>时间</th>
                        <th  colspan="3">对阵</th>
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
                        </td>
                        <td>{{$am['hscore']}} - {{$am['ascore']}}</td>
                        <td>
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
        <div id="Player" class="default" style="display: none;">
            <div class="h_a">
                <button class="on" value="host">{{$match['hname']}}</button>
                <button value="away">{{$match['aname']}}</button>
            </div>
            <div class="host">
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
                    @foreach($lineup['home'] as $hl)
                        <tr>
                            <td><p>{{$match['sport'] == 1 ? $hl['num'] : $hl['location']}}</p></td>
                            <td>{{$hl['name']}}</td>
                            <td>{{$hl['first'] == 1 ? '是' : '否'}}</td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="away" style="display: none;">
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
                        @foreach($lineup['away'] as $al)
                            <tr>
                                <td><p>{{$match['sport'] == 1 ? $al['num'] : $al['location']}}</p></td>
                                <td>{{$al['name']}}</td>
                                <td>{{$al['first'] == 1 ? '是' : '否'}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div id="Technology" style="display: none;">
            @if($hasTech)
            <div class="count default">
                <p class="title">本场技术统计</p>
                <table>
                    @foreach($tech as $t)
                    <tr>
                        <td><p style="width: {{  (isset($t['h_p']) && is_numeric($t['h_p'])) ? $t['h_p'] * 100 : 0  }}%;"></p></td>
                        <td>{{$t['h']}}</td>
                        <td>{{$t['name']}}</td>
                        <td>{{$t['a']}}</td>
                        <td><p style="width: {{  (isset($t['a_p']) && is_numeric($t['a_p'])) ? $t['a_p'] * 100 : 0  }}%;"></p></td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
            @if(isset($events) && count($events) > 0)
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
            @endif
        </div>
        @if($hasArticle)
        <div id="News" style="display: none;">
            @foreach($articles as $article)
            <a href="{{$article->url}}" target="_blank">
                <p class="imgbox" style="background: url({{$article['cover']}}) no-repeat center; background-size: cover;"></p>
                <p class="con">{{$article['title']}}</p>
            </a>
            @endforeach
        </div>
        @endif
        @if($hasVideos)
        <div id="Record" style="display: none;">
            @foreach($videos as $video)
            <?php $vTitle = $video->getVideoTitle(); ?>
            <div class="item">
                <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $video['id'], 'video')}}" target="_blank">
                    <p class="imgbox" style="background: url({{empty($video['cover']) ? env('CDN_URL').'/img/mobile/image_default_n.jpg' : $video['cover']}}) no-repeat center; background-size: cover;"></p>
                    <p class="con">{{$vTitle}}</p>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/videoPhone.js?time=201803031112"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection