@extends('pc.layout.anchor_base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/anchor.css">
    <style>
        body {
            padding-top: 60px;
        }
        #TableHead {
            top: 60px;
        }
    </style>
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            <div id="Hot">
                <p class="title">焦点赛事</p>
                @if(count($hotMatches) > 5)
                    <button class="left"></button>
                    <button class="right" disabled></button>
                @endif
                <div class="list">
                    @foreach($hotMatches as $hotMatch)
                        <?php
                        $match = $hotMatch->getMatch();
                        ?>
                        <div class="item">
                            <a href="/anchor/room/{{$hotMatch['room_id']}}.html" target="_blank">
                                <p class="time">{{$match['league']}}<span>{{date('y.m.d H:i',$match['time'])}}</span></p>
                                <div class="team">
                                    <p class="host"><img src="{{$match['hicon']}}">{{$match['hname']}}</p>
                                    @if($match['status'] > 0)
                                        <p class="vs"><span class="live">直播中</span></p>
                                    @else
                                        <p class="vs"><span>VS</span></p>
                                    @endif
                                    <p class="away"><img src="{{$match['aicon']}}">{{$match['aname']}}</p>
                                </div>
                                <p class="anchor">主播：{{$hotMatch->room->anchor->name}}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="Anchor">
                <p class="title">主播推荐</p>
                <div class="list">
                    @foreach($hotAnchors as $hotAnchor)
                        <div class="item">
                            <a href="/anchor/room/{{$hotAnchor->room->id}}.html" target="_blank">
                                <div class="imgbox" style="background: url({{$hotAnchor['icon']}}) no-repeat center; background-size: cover;"></div>
                                <p>{{$hotAnchor['name']}}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="Live">
                <p class="title">正在直播</p>
                <div class="list">
                    @foreach($livingRooms as $livingRoom)
                        <?php
//                        $match = $livingRoom->getLivingMatch();
                            $count = \Illuminate\Support\Facades\Redis::get('99_'.$livingRoom['id'].'_userCount')>0?\Illuminate\Support\Facades\Redis::get('99_'.$livingRoom['id'].'_userCount'):0;
                        ?>
                        <div class="item">
                            <a href="/anchor/room/{{$livingRoom['id']}}.html" target="_blank">
                                <div class="imgbox" style="background: url({{$livingRoom['cover']}}) no-repeat center; background-size: cover;"></div>
                                <div class="info">
                                    <img src="{{$livingRoom->anchor->icon}}">
                                    <p class="room">{{$livingRoom['title']}}</p>
                                    <p class="name"><span>{{$count}}人</span>{{$livingRoom->anchor->name}}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/anchor.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
            setADClose();
        }

        function scroll () {
            $('#Hot .list').animate({
                scrollLeft: $(this).width()
            }, 2000);//2秒滑动到指定位置
        }
    </script>
@endsection