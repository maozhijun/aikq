@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/home.css?time=20180126">
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            <table class="list" id="TableHead">
                <col number="1" width="64px">
                <col number="2" width="11.7%">
                <col number="3" width="6.8%">
                <col number="4" width="6.8%">
                <col number="5" width="14%">
                <col number="6" width="40px">
                <col number="7" width="46px">
                <col number="8" width="26px">
                <col number="9" width="46px">
                <col number="10" width="40px">
                <col number="11" width="14%">
                <col number="12" width="20%">
                <thead>
                <tr>
                    <th>项目</th>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>状态</th>
                    <th colspan="7">对阵</th>
                    <th>直播频道</th>
                </tr>
                </thead>
                <tbody>
                <tr><th colspan="12"></th></tr>
                </tbody>
            </table>
            <table class="list" id="Show">
                <col number="1" width="64px">
                <col number="2" width="11.7%">
                <col number="3" width="6.8%">
                <col number="4" width="6.8%">
                <col number="5" width="14%">
                <col number="6" width="40px">
                <col number="7" width="46px">
                <col number="8" width="26px">
                <col number="9" width="46px">
                <col number="10" width="40px">
                <col number="11" width="14%">
                <col number="12" width="20%">
                <thead>
                <tr>
                    <th>项目</th>
                    <th>赛事</th>
                    <th>时间</th>
                    <th>状态</th>
                    <th colspan="7">对阵</th>
                    <th>直播频道</th>
                </tr>
                </thead>
                <tbody>
                <?php $bj = 0;
                ?>
                @foreach($matches as $time=>$match_array)
                    <?php
                    $week = date('w', strtotime($time));
                    ?>
                    <tr class="date">
                        <th colspan="12">{{date_format(date_create($time),'Y年m月d日')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$week_array[$week]}}</th>
                    </tr>
                    @foreach($match_array as $match)
                        @component('pc.cell.home_match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
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
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/home.js?time=2018012619"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            setPage();
        }
    </script>
@endsection