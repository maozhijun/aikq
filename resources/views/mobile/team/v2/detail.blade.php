@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/match_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2//news_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2//video_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2//team_wap_2.css">
@endsection
@section('banner')
    <div id="Navigation">
        <a href="/" class="home"><img src="{{env('CDN_URL')}}/img/mobile/v2/logo_white.png"></a>
        <p class="inner_column_con">
            <a href="/">直播</a>
            <a href="/news/">资讯</a>
            <a href="/video/">视频</a>
            <a href="/record/">录像</a>
            <a href="/data/">数据</a>
        </p>
    </div>
@endsection
@section('content')
    <div id="Info_con">
        <div class="name_con">
            <img src="{{$team['icon']}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"'>
            <h1>{{$team['name']}}</h1>
            <p>现任主教练：-</p>
            <p>所在城市：{{$team['city']}}</p>
            <p>外文队名：{{isset($team['name_en']) ? $team['name_en'] :'-'}}</p>
            <p>球队主场：{{$team['gym']}}</p>
            <p>成立时间：{{strlen($team['establish']) > 0 ? $team['establish'] : "-"}}</p>
        </div>
        <div class="tab_con">
            <p class="tab_item on" forItem="match">赛程</p>
            <p class="tab_item" forItem="matchend">战绩</p>
            <p class="tab_item" forItem="player">阵容</p>
            <p class="tab_item" forItem="news">资讯</p>
            @if(count($videos) > 0)
                <p class="tab_item" forItem="video">视频</p>
            @endif
        </div>
    </div>
    <div class="match_con match" style="display: ;">
        <div class="match_list_con">
            @if(isset($lives['schedule']))
                @foreach($lives['schedule'] as $match)
                    <?php
                    $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $match['mid']);
                    $fv = \App\Models\Subject\SubjectVideo::firstVideo($match['mid']);
                    $st = $match['sport'] == 1 ? 'football' : 'basketball';
                    $et = $match['status'] > 0 ? 'live' : '';
                    ?>
                    <a href="{{$liveUrl}}" class="{{$st}} {{$et}}">
                        <div class="team_con">
                            <p>{{$match['hname']}}</p>
                            <p>{{$match['aname']}}</p>
                        </div>
                        <div class="info_con">
                            <p>{{date('m-d', $match['time'])}}</p>
                            <p>{{date('H:i', $match['time'])}}</p>
                        </div>
                        <div class="status_con"></div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
    <div class="match_con matchend" style="display: none;">
        <div class="match_list_con">
            @foreach($lives['recent'] as $match)
                <?php
                $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $match['mid']);
                $fv = \App\Models\Subject\SubjectVideo::firstVideo($match['mid']);
                $st = $match['sport'] == 1 ? 'football' : 'basketball';
                ?>
                <a href="{{$liveUrl}}" class="{{$st}} end">
                    <div class="team_con">
                        <p @if($match['hscore'] < $match['ascore'])class="lose"@endif><span>{{$match['hscore']}}</span>{{$match['hname']}}</p>
                        <p @if($match['hscore'] > $match['ascore'])class="lose"@endif><span>{{$match['ascore']}}</span>{{$match['aname']}}</p>
                    </div>
                    <div class="info_con">
                        <p>{{date('m-d', $match['time'])}}</p>
                        <p>{{date('H:i', $match['time'])}}</p>
                    </div>
                    <div class="status_con"></div>
                </a>
            @endforeach
        </div>
    </div>
    <div class="player_con player" style="display: none;">
        <table>
            <col width="16%"><col width="14%"><col>
            <tr>
                <th>位置</th>
                <th>号码</th>
                <th>姓名</th>
            </tr>
            @if(isset($team['lineup']) && count($team['lineup']) > 0)
                @foreach($team['lineup'] as $lineup)
                    <?php if (str_contains($lineup['position'], "教练")) $coach = $lineup['name']; ?>
                    <tr>
                        <td>{{$lineup['position']}}</td>
                        <td>{{strlen($lineup['num']) > 0 ? $lineup['num'] : '-'}}</td>
                        <td class="name">{{$lineup['name']}}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
    <div class="news_list_con news" style="display: none;">
        @foreach($articles as $article)
            <?php
            $tags = \App\Models\Tag\TagRelation::getTagWithSids(\App\Models\Tag\TagRelation::kTypeArticle,$article['id']);
            ?>
            <a href="{{$article['link']}}">
                <p class="img_box" style="background-image: url({{$article['cover']}})"></p>
                <h3>{{$article['title']}}</h3>
                <p class="date_con">{{date('m-d', date_create($article['update_at'])->getTimestamp())}}</p>
                @if(count($tags) > 0)
                    <p class="tag_con">
                        @foreach($tags as $key=>$tag)
                            <span>{{$tag['name']}}</span>
                        @endforeach
                    </p>
                @endif
            </a>
        @endforeach
    </div>
    @if(count($videos) > 0)
        <div class="video_list_con video" style="display: none;">
            @foreach($videos as $video)
                <div class="video_item">
                    <a href="{{\App\Models\Match\HotVideo::getVideoDetailUrl($video["id"])}}">
                        <p class="img_box" style="background-image: url({{$video["image"]}});"></p>
                        <h3>{{$video["title"]}}</h3>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/team_wap_2.js?time=201903140936"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage()
        }
    </script>
@endsection