@extends('mobile.layout.base')
@section('title')
    <title>爱看球-JRS|JRS直播|NBA直播|NBA录像|CBA直播|英超直播|西甲直播|低调看|直播吧|CCTV5在线</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/anchorPhone.css?rd=201804">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <!-- <p class="type"><button class="on" id="Football" name="type">足球</button><button id="Basketball" name="type">篮球</button><button id="Other" name="type">其他</button></p> -->
            <img src="{{env('CDN_URL')}}/img/mobile/image_slogan_nav.png">
        </div>
    </div>
@endsection
@section('content')
    <div id="Hot">
        <div class="inner">
            @foreach($hotMatches as $hotMatch)
                <?php
                $match = $hotMatch->getMatch();
                ?>
                <a href="/m/anchor/room/{{$hotMatch['room_id']}}.html" class="item {{$match['status'] > 0 ? 'live':''}}">
                    <div class="match">
                        <p class="team">
                            <img src="//static.liaogou168.com/images/team/fb/608/5b1787e90c407.jpg">
                            {{$match['hname']}}
                        </p>
                        <p class="team">
                            <img src="//static.liaogou168.com/images/team/fb/584/5b1787e505b00.jpg">
                            {{$match['aname']}}
                        </p>
                        <p class="anchor">主播：{{$hotMatch->room->anchor->name}}</p>
                    </div>
                </a>
            @endforeach
            <div class="item empty">
                <div class="match">
                    <p class="team"></p>
                    <p class="team"></p>
                    <p class="anchor"></p>
                </div>
            </div>
            <div class="item empty">
                <div class="match">
                    <p class="team"></p>
                    <p class="team"></p>
                    <p class="anchor"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="default" id="Anchor">
        <div class="title">
            <p>热门主播</p>
        </div>
        <ul>
            @foreach($hotAnchors as $hotAnchor)
                <li><a href="/m/anchor/room/{{$hotAnchor->room->id}}.html"><img src="{{$hotAnchor['icon'] or env('CDN_URL').'/img/mobile/image_player_n.jpg'}}"><p>{{$hotAnchor['name']}}</p></a></li>
            @endforeach
        </ul>
    </div>
    <div class="default" id="Live">
        <div class="title">
            <p>正在直播</p>
        </div>
        <ul>
            @foreach($livingRooms as $livingRoom)
                <li><a href="/m/anchor/room/{{$livingRoom['id']}}.html">
                        <div class="imgbox" style="background: url('{{$livingRoom['cover']}}'); background-size: cover;"><p>{{$livingRoom->anchor->name}}</p></div>
                        <p class="name">{{$livingRoom['title']}}</p>
                    </a></li>
            @endforeach
        </ul>
    </div>
@endsection
@section('bottom')
    <dl id="Bottom">
        <dd>
            <a href="/m/lives.html">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_live_n.png">
                <p>直播</p>
            </a>
        </dd>
        <dd class="on">
            <a>
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_anchor_s.png">
                <p>主播</p>
            </a>
        </dd>
        <dd>
            <a href="">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_vedio_n.png">
                <p>录像</p>
            </a>
        </dd>
        <dd>
            <a href="https://shop.liaogou168.com">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_recommend_n.png">
                <p>推荐</p>
            </a>
        </dd>
    </dl>
    @endsection
@section('js')
    <script type="text/javascript">

    </script>
@endsection