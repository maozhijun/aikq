@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/home.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/recording.css">
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
            <div class="moreVideo">
                <div class="labelbox">
                    @foreach($leagues as $lid=>$league)
                        @if(isset($league['count']) && isset($league['name']) && $league['count'] > 0)
                            <button id="{{$lid}}" class="{{$lid == $type ? 'on' : ''}}">{{$league['name']}}</button>
                        @endif
                    @endforeach
                </div>
            </div>
            {{--<table class="list" id="TableHead">--}}
                {{--<col number="1" width="50px">--}}
                {{--<col number="2" width="80px">--}}
                {{--<col number="3" width="70px">--}}
                {{--<col number="4" width="16%">--}}
                {{--<col number="5" width="12px">--}}
                {{--<col number="6" width="26px">--}}
                {{--<col number="7" width="12px">--}}
                {{--<col number="8" width="16%">--}}
                {{--<col number="9" width="360px">--}}
                {{--<thead>--}}
                {{--<tr>--}}
                    {{--<th>项目</th>--}}
                    {{--<th>赛事</th>--}}
                    {{--<th>时间</th>--}}
                    {{--<th colspan="5">对阵</th>--}}
                    {{--<th>直播频道</th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody>--}}
                {{--<tr><th colspan="9"></th></tr>--}}
                {{--</tbody>--}}
            {{--</table>--}}
            <table class="list" id="Show" style="margin: 0 auto;">
                <col number="1" width="50px">
                <col number="2" width="80px">
                <col number="3" width="70px">
                <col number="4" width="22%">
                <col number="5" width="12px">
                <col number="6" width="26px">
                <col number="7" width="12px">
                <col number="8" width="22%">
                <col number="9" width="200px">
                {{--<thead>--}}
                {{--<tr>--}}
                    {{--<th>项目</th>--}}
                    {{--<th>赛事</th>--}}
                    {{--<th>时间</th>--}}
                    {{--<th colspan="5">对阵</th>--}}
                    {{--<th>直播频道</th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                <tbody>
                <?php $bj = 0; ?>
                @foreach($matches as $time=>$match_array)
                    <?php $week = date('w', $time); ?>
                    <tr class="date">
                        <th colspan="9">{{date('Y年m月d日', $time)}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$week_array[$week]}}</th>
                    </tr>
                    @foreach($match_array as $match)
                        @component('pc.cell.video_match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div id="Page" curPage="{{$page['curPage']}}" lastPage="{{$page['lastPage']}}" style="display: none;"></div>
    {{--<div class="adflag left">--}}
        {{--<button class="close" onclick="document.body.removeChild(this.parentNode)"></button>--}}
        {{--<a><img src="/img/pc/ad/double.jpg"></a>--}}
        {{--<br>--}}
        {{--<a href="http://91889188.87.cn" target="_blank"><img src="/img/pc/ad_zhong_double.jpg"></a>--}}
    {{--</div>--}}
    {{--<div class="adflag right">--}}
        {{--<button class="close" onclick="document.body.removeChild(this.parentNode)"></button>--}}
        {{--<a href="http://91889188.87.cn" target="_blank"><img src="/img/pc/ad_zhong_double.jpg"></a>--}}
        {{--<br>--}}
        {{--<a><img src="/img/pc/ad/double.jpg"></a>--}}
    {{--</div>--}}
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/home.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/subject_recording.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            setPage();
            createPageHtml('Page');
            bindPageA("Page");
            bindType();
        }
    </script>
@endsection