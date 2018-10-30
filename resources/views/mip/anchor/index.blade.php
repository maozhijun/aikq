@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/anchorPhone.css?rd=201808141800">
@endsection
@section('banner')
    <div id="Navigation">
        @if(isset($h1))
            <h1>{{$h1}}</h1>
        @endif
        <div class="banner">
            <mip-img height="26" width="75" src="{{env('CDN_URL')}}/img/mip/image_slogan_nav.png"></mip-img>
        </div>
    </div>
@endsection
@section('content')
    <div class="default" id="Anchor">
        <div class="title">
            <p>热门主播</p>
        </div>
        <ul>
            @foreach($hotAnchors as $hotAnchor)
                <li><a href="{{\App\Http\Controllers\Mip\UrlCommonTool::anchorRoomUrl($hotAnchor->room->id)}}"><mip-img width="40" height="40" src="{{isset($hotAnchor['icon']) ? $hotAnchor['icon'] : env('STATIC_URL').'/img/pc/image_player_n.jpg'}}"></mip-img><p>{{$hotAnchor['name']}}</p></a></li>
            @endforeach
        </ul>
    </div>
    <div class="default" id="Live">
        <div class="title">
            <p>正在直播</p>
        </div>
        <ul>
            @foreach($livingRooms as $livingRoom)
                <li><a href="{{\App\Http\Controllers\Mip\UrlCommonTool::anchorRoomUrl($livingRoom['id'])}}">
                        <div class="imgbox">
                            <mip-img layout="fixed-height" height="110" src="{{$livingRoom['live_cover']}}"></mip-img>
                            <p>{{$livingRoom->anchor->name}}</p>
                        </div>
                        <p class="name">{{$livingRoom['title']}}</p>
                    </a></li>
            @endforeach
            <div class="nolist separated">
                <mip-img height="110" width="110" src="{{env('CDN_URL')}}/img/pc/image_blank_noneanchor_n.png"></mip-img>
                <p>还没有主播在直播喔~</p>
            </div>
        </ul>
    </div>
@endsection
@section('bottom')
    @include("mip.layout.bottom_cell", ['cur'=>'anchor'])
@endsection