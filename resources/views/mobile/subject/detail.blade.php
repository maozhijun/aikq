@extends('mobile.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/league.css?time=20180000000002">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <a class="home" href="/"></a>
            @if(isset($h1))
                <h1>{{$h1}}</h1>
            @endif
        </div>
    </div>
    <div class="tab">
        <p class="on" type="Live">直播</p>
        <p type="News">资讯</p>
        {{--<p type="Recording">录像</p>--}}
        <p type="Rank">积分榜</p>
    </div>
@endsection

@section('content')
    <div id="Live" style="display: ;">
        @foreach($lives as $day=>$matches)
            <p class="day">{{date('Y-m-d', $day)}}&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</p>
            @foreach($matches as $match)
                <?php
                $url = "javascript:void(0)";
                $className = "unload";
                $impt_style = '';
                if (array_key_exists('channels', $match)) {
                    $channels = $match['channels'];
                    $isMatching = $match['status']>0 || (isset($match['isMatching']) && $match['isMatching']);
                    if (isset($channels) && count($channels) > 0) {
                        $firstChannel = $channels[0];
                        $impt = $firstChannel['impt'];
                        if ($impt == 2) {
                            $impt_style = 'style="color:#bc1c25;"';
                        }
                        $url = $firstChannel['live_url'];//\App\Http\Controllers\Mobile\UrlCommonTool::matchLiveUrl($lid,$match['sport'],$match['mid']);
                        $className = $isMatching ? "live" : "";
                    }
                }
                ?>
                <a href="{{$url}}" @if(strlen($className) > 0)class="{{$className}}" @endif>
                    <p class="time">{{date('H:i', $match['time'])}}</p>
                    <p {!! $impt_style !!} class="match">{{$match['hname']}}<span>@if($match['status'] == 0) vs @else {{$match['hscore'] . ' - ' . $match['ascore']}} @endif</span>{{$match['aname']}}</p>
                </a>
            @endforeach
        @endforeach
    </div>
    <div id="News" style="display: none;">
        @if(isset($articles) && count($articles) > 0)
            @foreach($articles as $article)
                <?php $a_cover = isset($article['cover']) ? $article['cover'] : ''; ?>
                <a href="{{$article["link"]}}" class="li">
                    @if(!empty($a_cover)) <div class="imgbox" style="background: url({{$a_cover}}) no-repeat center; background-size: cover;"></div> @endif
                    <h6>{{$article["title"]}}</h6>
                    <p class="info">{{date("Y.m.d", strtotime($article["update_at"]))}}&nbsp;&nbsp;{{date("H:i", strtotime($article["update_at"]))}}</p>
                </a>
            @endforeach
        @else
        @endif
    </div>
    {{--<div id="Recording" style="display: none;">--}}
        {{--@if(isset($videos) && count($videos) > 0)--}}
            {{--@foreach($videos as $day=>$matches)--}}
                {{--<p class="day">{{date('Y-m-d', $day)}}&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</p>--}}
                {{--@foreach($matches as $match)--}}
                    {{--<a href="{{\App\Http\Controllers\Mobile\UrlCommonTool::matchVideoUrl($match['id'])}}">@if(isset($match['time']))<p class="time">{{date('H:i', strtotime($match['time']))}}</p>@endif<p class="match">{{$match['hname']}} vs {{$match['aname']}}</p></a>--}}
                {{--@endforeach--}}
            {{--@endforeach--}}
        {{--@else--}}
        {{--@endif--}}
    {{--</div>--}}
    <div id="Rank" style="display: none;">
        @if(isset($ranks) && count($ranks) > 0)
            <div class="in">
                @if(array_key_exists(0, $ranks))
                    <?php
                    $rank = $ranks[0];
                    ?>
                    <div class="title">
                        <p class="rank">排名</p>
                        <p class="team">球队</p>
                        @if(array_key_exists('draw',$rank))
                            <p class="wdl">胜/平/负</p>
                        @else
                            <p class="wdl">胜/负</p>
                        @endif
                        @if(array_key_exists('draw',$rank))
                            <p class="gl">得/失</p>
                        @endif
                        @if(array_key_exists('draw',$rank))
                            <p class="score">积分</p>
                        @else
                            <p class="score">胜率</p>
                        @endif
                    </div>
                    @foreach($ranks as $key=>$rank)
                        <div class="list">
                            <p class="rank">{{$key+1}}</p>
                            @if(isset($rank['tid']))
                                <p class="team"><a target="_blank" href="{{\App\Http\Controllers\Mobile\UrlCommonTool::getTeamDetailUrl($rank['sport'], $rank['lid'], $rank['tid'])}}">{{$rank['name']}}</a></p>
                            @else
                                <p class="team">{{$rank['name']}}</p>
                            @endif
                            @if(array_key_exists('draw',$rank))
                                <p class="wdl">{{$rank['win']}}/{{$rank['draw']}}/{{$rank['lose']}}</p>
                            @else
                                <p class="wdl">{{$rank['win']}}/{{$rank['lose']}}</p>
                            @endif
                            @if(array_key_exists('draw',$rank))
                                <p class="gl">{{$rank['score']}}/{{$rank['lose']}}</p>
                            @endif
                            @if(array_key_exists('draw',$rank))
                                <p class="score">{{$rank['score']}}</p>
                            @else
                                <p class="score">{{$rank['win_p']}}</p>
                            @endif
                        </div>
                    @endforeach
            </div>
        @else
            @foreach($ranks as $group=>$groupRanks)
                <div class="in">
                    <div class="title">
                        @if($group == 'west')
                            <p class="rank">西岸</p>
                        @elseif($group == 'east')
                            <p class="rank">东岸</p>
                            @else
                            <p class="rank">{{$group}}组</p>
                        @endif
                        <p class="team">球队</p>
                        @if(isset($rank['draw']))
                            <p class="wdl">胜/平/负</p>
                        @else
                            <p class="wdl">胜/负</p>
                        @endif
                        @if(isset($rank['draw']))
                            <p class="gl">得/失</p>
                        @endif
                        @if(isset($rank['draw']))
                            <p class="score">积分</p>
                        @else
                            <p class="score">胜率</p>
                        @endif
                    </div>
                    @foreach($groupRanks as $key=>$rank)
                        <div class="list">
                            <p class="rank">{{$key+1}}</p>
                            @if(isset($rank['tid']))
                                <p class="team"><a target="_blank" href="{{\App\Http\Controllers\Mobile\UrlCommonTool::getTeamDetailUrl($rank['sport'], $rank['lid'], $rank['tid'])}}">{{$rank['name']}}</a></p>
                            @else
                                <p class="team">{{$rank['name']}}</p>
                            @endif
                            @if(isset($rank['draw']))
                                <p class="wdl">{{$rank['win']}}/{{$rank['draw']}}/{{$rank['lose']}}</p>
                            @else
                                <p class="wdl">{{$rank['win']}}/{{$rank['lose']}}</p>
                            @endif
                            @if(isset($rank['draw']))
                                <p class="gl">{{$rank['score']}}/{{$rank['lose']}}</p>
                            @endif
                            @if(isset($rank['draw']))
                                <p class="score">{{$rank['score']}}</p>
                            @else
                                <p class="score">{{$rank['win_p']}}%</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
        @endif
    </div>
@stop

@section('js')
    <script type="text/javascript">
        window.onload = function () {
            $('.tab p').click(function(){
                if (!$(this).hasClass('on')) {
                    $('.tab p.on').removeClass('on');
                    $('#Live,#News,#Recording,#Rank').css('display','none');

                    $(this).addClass('on');
                    $('#' + $(this).attr('type')).css('display','');

                    $('html,body').animate({scrollTop: 0},0);
                }
            })
        }
    </script>
@endsection