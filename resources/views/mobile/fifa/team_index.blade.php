@extends('mobile.fifa.base')
@section('content')
    <div id="Group">
        @foreach($group as $key=>$data)
            <p @if($key == 'A')class="item on"@else class="item" @endif for="{{$key}}">{{$key}}组</p>
        @endforeach
    </div>
    @foreach($group as $key=>$data)
        <div class="team" id="{{$key}}" @if($key != 'A')style="display: none" @endif>
            @foreach($data['scores'] as $team)
                <a class="box" href="/m/worldcup/2018/team/{{$team['tid']}}.html">
                    <img class="bg" src="{{isset($team['bg_img'])?$team['bg_img']:''}}" onerror="this.src = 'https://gss1.bdstatic.com/9vo3dSag_xI4khGkpoWK1HF6hhy/baike/crop%3D14%2C0%2C521%2C343%3Bc0%3Dbaike80%2C5%2C5%2C80%2C26/sign=9ae69e9b61600c33e4368488277d6523/c75c10385343fbf24394fbacba7eca8064388f81.jpg'">
                    <div class="icon" style="background: url({{$team['ticon']}}) no-repeat center #fff; background-size: contain"></div>
                    <p>{{$team['tname']}}</p>
                </a>
            @endforeach
        </div>
    @endforeach
    @component('mobile.fifa.base_bottom',['index'=>1])
    @endcomponent
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/team.js?time=201803030002"></script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/team.css">
@endsection
