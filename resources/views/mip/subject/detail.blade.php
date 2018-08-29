@extends('mip.layout.base')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{\App\Http\Controllers\Mip\UrlCommonTool::MIP_PREFIX}}/css/league.css">
@endsection
@section('js')
    <script src="https://c.mipcdn.com/static/v1/mip-vd-tabs/mip-vd-tabs.js"></script>
    <script src="https://c.mipcdn.com/static/v1/mip-infinitescroll/mip-infinitescroll.js"></script>
    <script src="https://c.mipcdn.com/static/v1/mip-mustache/mip-mustache.js"></script>
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <h1>{{$subject['name']}}</h1>
        </div>
    </div>
@endsection

@section('content')
    <mip-vd-tabs class="tab">
        <section>
            <li>直播</li>
            <li>资讯</li>
            {{--<li>录像</li>--}}
            <li>积分榜</li>
        </section>
        <div id="Live"  class="inner">
            @foreach($lives as $day=>$matches)
                <p class="day">{{date('Y-m-d', $day)}}&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</p>
                @foreach($matches as $match)
                    <a href="{{\App\Http\Controllers\Mip\UrlCommonTool::matchLiveUrl($lid,$match['sport'],$match['mid'])}}" @if($match['status']>0)class="live" @endif>
                        <p class="time">{{date('H:i', $match['time'])}}</p>
                        <p class="match">{{$match['hname']}}<span>@if($match['status'] == 0) vs @else {{$match['hscore'] . ' - ' . $match['ascore']}} @endif</span>{{$match['aname']}}</p>
                    </a>
                @endforeach
            @endforeach
        </div>
        <div id="News" class="inner">
            @if(isset($articles) && count($articles) > 0)
                @foreach($articles as $article)
                    <a href="{{$article["link"]}}" class="li">
                        <mip-img height="66" layout="fixed-height" src="https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2148120987,1371891926&fm=27&gp=0.jpg"></mip-img>
                        <h6>{{$article["title"]}}</h6>
                        {{--                    <p class="info">{{date("Y.m.d", strtotime($article["update_at"]))}}&nbsp;&nbsp;{{date("H:i", strtotime($article["update_at"]))}}</p>--}}
                    </a>
                @endforeach
            @else
            @endif
        </div>
        {{--<div id="Recording" class="inner">--}}
            {{--@if(isset($videos) && count($videos) > 0)--}}
                {{--@foreach($videos as $day=>$matches)--}}
                    {{--<p class="day">{{date('Y-m-d', $day)}}&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</p>--}}
                    {{--@foreach($matches as $match)--}}
                        {{--<div class="item"><a href="{{\App\Http\Controllers\Mip\UrlCommonTool::matchVideoUrl($match['id'])}}">{{$match['hname']}} vs {{$match['aname']}}<span><mip-img src="http://img5.imgtn.bdimg.com/it/u=236407236,1342164149&fm=26&gp=0.jpg"></mip-img></span></a></div>--}}
                    {{--@endforeach--}}
                {{--@endforeach--}}
            {{--@else--}}
            {{--@endif--}}
        {{--</div>--}}
        <div id="Rank" class="inner">
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
                                <p class="team">{{$rank['name']}}</p>
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
                                <p class="team">{{$rank['name']}}</p>
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
    </mip-vd-tabs>
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