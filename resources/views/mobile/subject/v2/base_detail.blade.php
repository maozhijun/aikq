@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/match_list_wap_2.css?time=201903141213">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/news_list_wap_2.css?time=201903141213">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/video_list_wap_2.css?time=201903141213">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/league_wap_2.css?time=201903141213">
@endsection
@section('banner')
    @include('mobile.layout.v2.top_nav_cell')
@endsection

@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/league_wap_2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage()
        }
    </script>
@endsection