@extends('mobile.fifa.base')
@section('content')
    <div class="bigBg" style="background: url({{isset($team['bg_img'])?$team['bg_img']:'https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike80%2C5%2C5%2C80%2C26/sign=d7f09c74a08b87d6444fa34d6661435d/203fb80e7bec54e7523d345bb8389b504ec26aa5.jpg'}}) no-repeat center; background-size: cover;">
        <div class="icon"  style="background: url({{$team['icon']}}) no-repeat center #fff; background-size: contain"></div>
    </div>
    <div id="Tab">
        <p class="item on" for="Match">赛程赛果</p>
        <p class="item" for="Player">教练球员</p>
        <p class="item" for="Info">详细介绍</p>
    </div>
    <div class="matchList" id="Match">
        @foreach($matches as $match)
            @component('mobile.fifa.match_cell',['match'=>$match])
            @endcomponent
        @endforeach
    </div>
    <ul id="Player" style="display: none;">
        @if(count($lineup['coach']) > 0)
            <li>
                <p class="coach">教</p>
                <img src="{{env('CDN_URL')}}/img/mobile/fifa/image_player_n.jpg">
                <p class="name">{{$lineup['coach'][0]['name']}}</p>
            </li>
        @endif
        @foreach($lineup['lineup'] as $player)
            <li>
                <p class="number">{{$player['num']}}</p>
                <img src="{{$player['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/mobile/fifa/image_player_n.jpg'">
                <p class="name">{{$player['name']}}</p>
                <p class="site">{{$player['pos']}}</p>
            </li>
        @endforeach
    </ul>
    <div id="Info" style="display: none;">
        <div class="in">
            <p>{{$team['describe']}}</p>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/teamEnd.js?time=201803030002"></script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/teamEnd.css">
@endsection
