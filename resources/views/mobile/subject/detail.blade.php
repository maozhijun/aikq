@extends('mobile.layout.base')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/league.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <h1>{{$subject['name']}}</h1>
        </div>
    </div>
    <div class="tab">
        <p class="on" type="Live">直播</p>
        <p type="News">资讯</p>
        <p type="Recording">录像</p>
        <p type="Rank">积分榜</p>
    </div>
@endsection

@section('content')
    <div id="Live" style="display: ;">
        @foreach($lives as $day=>$matches)
            <p class="day">{{date('Y-m-d', $day)}}&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</p>
            @foreach($matches as $match)
                <a href="videoPhone.html" @if($match['status']>0)class="live" @endif>
                    <p class="time">{{date('H:i', $match['time'])}}</p>
                    <p class="match">{{$match['hname']}}<span>@if($match['status'] == 0) vs @else {{$match['hscore'] . ' - ' . $match['ascore']}} @endif</span>{{$match['aname']}}</p>
                </a>
            @endforeach
        @endforeach
    </div>
    <div id="News" style="display: none;">
        @if(isset($articles) && count($articles) > 0)
            @foreach($articles as $article)
                <a href="{{$article["link"]}}" class="li">
                    <div class="imgbox" style="background: url(https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2148120987,1371891926&fm=27&gp=0.jpg) no-repeat center; background-size: cover;"></div>
                    <h6>{{$article["title"]}}</h6>
{{--                    <p class="info">{{date("Y.m.d", strtotime($article["update_at"]))}}&nbsp;&nbsp;{{date("H:i", strtotime($article["update_at"]))}}</p>--}}
                </a>
            @endforeach
        @else
        @endif
    </div>
    <div id="Recording" style="display: none;">
        @if(isset($videos) && count($videos) > 0)
            @foreach($videos as $day=>$matches)
                <p class="day">{{date('Y-m-d', $day)}}&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</p>
                @foreach($matches as $match)
                    <div class="item"><a href="">{{$match['hname']}} vs {{$match['aname']}}<span><img src="http://img5.imgtn.bdimg.com/it/u=236407236,1342164149&fm=26&gp=0.jpg"></span></a></div>
                @endforeach
            @endforeach
        @else
        @endif
    </div>
    <div id="Rank" style="display: none;">
        <div class="title">
            <p class="rank">排名</p>
            <p class="team">球队</p>
            <p class="wdl">胜/平/负</p>
            <p class="gl">得/失</p>
            <p class="score">积分</p>
        </div>
        @if(isset($ranks) && count($ranks) > 0)
            @foreach($ranks as $rank)
                <div class="list">
                    <p class="rank">{{$rank['rank']}}</p>
                    <p class="team">{{$rank['name']}}</p>
                    <p class="wdl">{{$rank['win']}}/{{$rank['draw']}}/{{$rank['lose']}}</p>
                    <p class="gl">{{$rank['score']}}/{{$rank['lose']}}</p>
                    <p class="score">{{$rank['score']}}</p>
                </div>
            @endforeach
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