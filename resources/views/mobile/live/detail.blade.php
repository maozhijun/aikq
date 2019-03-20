@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/news_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/video_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/live_wap_2.css">
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
$mid = $match["mid"];
while (strlen($mid) < 4) {
    $mid = "0".$mid;
}
?>
@section("banner")
    <div id="Navigation">
        <a href="/" class="home"><img src="{{env('CDN_URL')}}/img/mobile/v2/logo_white.png"></a>
        <p class="inner_column_con">
            <a href="/">直播</a>
            <a href="/news/">资讯</a>
            <a href="/video/">视频</a>
            <a href="/record/" class="on">录像</a>
            <a href="/data/">数据</a>
        </p>
    </div>
@endsection

@section('content')
    <div id="Match_info">
        <div class="info_con">
            <a class="team_con" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match["sport"],$lid,$match['hid'])}}">
                <img src="{{$host_icon or ''}}" onerror="this.src='{{env('CDN_URL').'/img/pc/icon_teamDefault.png'}}'">
                <p>{{$match['hname']}}</p>
            </a>
            <div class="vs_con">
                <p>{{$match['win_lname']}}</p>
                <p>{{substr($match['time'], 2, 14)}}</p>
                <div class="forselect">视频直播</div>
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
                            $url = env('LHB_URL') . '/room/' . $match["sport"] . $mid . '.html?btn='.($btnIndex++);
                        }
                        $ex = 1;
                        ?>
                        <option value="{{$url}}">{{$channel['name']}}</option>
                    @endforeach
                    <option value="https://xinhi.com/live_detail/{{$match['sport']}}_{{$mid}}.html">备用信号</option>
                </select>
            </div>
            <a class="team_con" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match["sport"],$lid,$match['aid'])}}">
                <img src="{{$away_icon or ''}}" onerror="this.src='{{env('CDN_URL').'/img/pc/icon_teamDefault.png'}}'">
                <p>{{$match['aname']}}</p>
            </a>
        </div>
        <div class="tab_con">
            <p class="tab_item on" forItem="Data">数据分析</p>
            <p class="tab_item" forItem="News">相关新闻</p>
            <p class="tab_item" forItem="Video">相关视频</p>
        </div>
    </div>
    <div class="app_full_con">
        <img src="{{env('CDN_URL').'/img/mobile/v2/img_home_appdown_wap.jpg'}}">
        <button class="close"></button>
    </div>


    <div id="Data" style="display: ;">
        @if(isset($passVSMatches) && count($passVSMatches) > 0)
            <p class="title_text">对赛往绩</p>
            <table>
                <tr>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>对阵</th>
                    <th>录像</th>
                </tr>
                <tbody>
                @foreach($passVSMatches as $pm)
                    <?php
                    $pDate = date('Y-m-d H:i', strtotime($pm['time']));
                    $fv = \App\Models\Subject\SubjectVideo::firstVideo($pm['id']);
                    ?>
                    <tr>
                        <td>{{$pm['win_lname']}}</td>
                        <td><p>{{substr($pDate, 2 , 5)}}</p><p>{{substr($pDate, 10, 6)}}</p></td>
                        <td>
                            <p @if($pm['hscore'] > $pm['ascore'])class="win"@endif><span>{{$pm['hscore']}}</span>{{$pm['hname']}}</p>
                            <p @if($pm['hscore'] < $pm['ascore'])class="win"@endif><span>{{$pm['ascore']}}</span>{{$pm['aname']}}</p>
                        </td>
                        <td>
                            @if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">查看 ></a>@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        @if(isset($hNearMatches) && count($hNearMatches) > 0)
            <p class="title_text">{{$match['hname']}}近期战绩</p>
            <table>
                <tr>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>对阵</th>
                    <th>录像</th>
                </tr>
                <tbody>
                @foreach($hNearMatches as $hm)
                    <?php
                    $pDate = date('Y-m-d H:i', strtotime($hm['time']));
                    $fv = \App\Models\Subject\SubjectVideo::firstVideo($hm['id']);
                    ?>
                    <tr>
                        <td>{{$hm['win_lname']}}</td>
                        <td><p>{{substr($pDate, 2 , 5)}}</p><p>{{substr($pDate, 10, 6)}}</p></td>
                        <td>
                            <p @if($hm['hscore'] > $hm['ascore'])class="win"@endif><span>{{$hm['hscore']}}</span>{{$hm['hname']}}</p>
                            <p @if($hm['hscore'] < $hm['ascore'])class="win"@endif><span>{{$hm['ascore']}}</span>{{$hm['aname']}}</p>
                        </td>
                        <td>
                            @if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">查看 ></a>@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        @if(isset($aNearMatches) && count($aNearMatches) > 0)
            <p class="title_text">{{$match['aname']}}近期战绩</p>
            <table>
                <tr>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>对阵</th>
                    <th>录像</th>
                </tr>
                <tbody>
                @foreach($aNearMatches as $am)
                    <?php
                    $pDate = date('Y-m-d H:i', strtotime($am['time']));
                    $fv = \App\Models\Subject\SubjectVideo::firstVideo($am['id']);
                    ?>
                    <tr>
                        <td>{{$am['win_lname']}}</td>
                        <td><p>{{substr($pDate, 2 , 5)}}</p><p>{{substr($pDate, 10, 6)}}</p></td>
                        <td>
                            <p @if($am['hscore'] > $am['ascore'])class="win"@endif><span>{{$am['hscore']}}</span>{{$am['hname']}}</p>
                            <p @if($am['hscore'] < $am['ascore'])class="win"@endif><span>{{$am['ascore']}}</span>{{$am['aname']}}</p>
                        </td>
                        <td>
                            @if(isset($fv))<a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">查看 ></a>@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @if($hasArticle)
        <div id="News" class="news_list_con" style="display: none;">
            @foreach($articles as $article)
                <a href="{{$article->url}}">
                    <p class="img_box" style="background-image: url({{$article['cover']}})"></p>
                    <h3>{{$article['title']}}</h3>
                    <p class="date_con">{{date('m-d', strtotime($article->publish_at))}}</p>
                    <p class="tag_con">
                        @foreach(explode(',', $article->labels) as $tag)
                            <span>{{$tag}}</span>
                        @endforeach
                    </p>
                </a>
            @endforeach
        </div>
    @endif
    @if(isset($comboData) && isset($comboData['videos']))
        <div id="Video" class="video_list_con" style="display: none;">
            @foreach($comboData['videos'] as $video)
                <div class="video_item">
                    <a href="{{$video['link']}}">
                        <p class="img_box" style="background-image: url({{$video['image']}});"></p>
                        <h3>{{$video['title']}}</h3>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/mobile/v2/live_wap_2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection