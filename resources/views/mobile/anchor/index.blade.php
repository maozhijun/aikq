@extends('mobile.layout.base')
@section('title')
    <title>美女主播球赛讲解_主播频道-爱看球直播</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/anchorPhone.css?rd=201808141800">
@endsection
@section('banner')
    <div id="Navigation">
        @if(isset($h1))
            <h1>{{$h1}}</h1>
        @endif
        <div class="banner">
            <!-- <p class="type"><button class="on" id="Football" name="type">足球</button><button id="Basketball" name="type">篮球</button><button id="Other" name="type">其他</button></p> -->
            <img src="{{env('CDN_URL')}}/img/mobile/image_slogan_nav.png">
        </div>
    </div>
@endsection
@section('content')
    @if(isset($hotMatches) && count($hotMatches) > 0)
    <div id="Hot">
        <div class="inner">
            @foreach($hotMatches as $hotMatch)
                <?php
                $match = $hotMatch->getMatch();
                ?>
                <a href="/anchor/room{{$hotMatch['room_id']}}.html" class="item {{$match['status'] > 0 ? 'live':''}}">
                    <div class="match">
                        <p class="team">
                            <img src="{{$match['hicon']}}" onerror="this.src='/img/pc/icon_teamlogo_n.png'">
                            {{$match['hname']}}
                        </p>
                        <p class="team">
                            <img src="{{$match['aicon']}}" onerror="this.src='/img/pc/icon_teamlogo_n.png'">
                            {{$match['aname']}}
                        </p>
                        <p class="anchor">主播：{{$hotMatch->room->anchor->name}}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
    <div class="default" id="Anchor">
        <div class="title">
            <p>热门主播</p>
        </div>
        <ul>
            @foreach($hotAnchors as $hotAnchor)
                <li><a href="/anchor/room{{$hotAnchor->room->id}}.html"><img src="{{$hotAnchor['icon']}}"  onerror="this.src='/img/pc/image_default_head.png'"><p>{{$hotAnchor['name']}}</p></a></li>
            @endforeach
        </ul>
    </div>
    <div class="default" id="Live">
        <div class="title">
            <p>正在直播</p>
        </div>
        <ul>
            @foreach($livingRooms as $livingRoom)
                <li><a href="/anchor/room{{$livingRoom['id']}}.html">
                        <div class="imgbox">
                            <img src="{{$livingRoom['live_cover']}}" onerror="this.src='/img/pc/image_bg_room.jpg'">
                            <p>{{$livingRoom->anchor->name}}</p>
                        </div>
                        <p class="name">{{$livingRoom['title']}}</p>
                    </a></li>
            @endforeach
                <div class="nolist separated">
                    <img src="/img/pc/image_blank_noneanchor_n.png">
                    <p>还没有主播在直播喔~</p>
                </div>
        </ul>
    </div>
@endsection
@section('bottom')
    @component("mobile.layout.bottom_cell", ['cur'=>'anchor']) @endcomponent
@endsection
@section('js')
    <script type="text/javascript">

    </script>
@endsection