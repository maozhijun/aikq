@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/home.css?time=201808161235">
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            <table class="list" id="TableHead">
                <col number="1" width="50px">
                <col number="2" width="80px">
                <col number="3" width="70px">
                <col number="4" width="16%">
                <col number="5" width="12px">
                <col number="6" width="26px">
                <col number="7" width="12px">
                <col number="8" width="16%">
                <col number="9" width="300px">
                <col number="10" width="60px">
                <thead>
                <tr>
                    <th>项目</th>
                    <th>赛事</th>
                    <th>时间</th>
                    <th colspan="5">对阵
                        <div class="choose">
                            <p>全部</p>
                            <ul>
                                <li value="all" class="on">全部</li>
                                <li value="lottery">竞彩</li>
                                <li value="first">一级</li>
                                <li value="imp">重要</li>
                            </ul>
                        </div>
                    </th>
                    <th>直播频道</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                <tr><th colspan="10"></th></tr>
                </tbody>
            </table>
            <table class="list" id="Show">
                <col number="1" width="50px">
                <col number="2" width="80px">
                <col number="3" width="70px">
                <col number="4" width="16%">
                <col number="5" width="12px">
                <col number="6" width="26px">
                <col number="7" width="12px">
                <col number="8" width="16%">
                <col number="9" width="300px">
                <col number="10" width="60px">
                <thead>
                <tr>
                    <th>项目</th>
                    <th>赛事</th>
                    <th>时间</th>
                    <th colspan="5">
                        对阵
                        <div class="choose">
                            <p>全部</p>
                            <ul>
                                <li value="all" class="on">全部</li>
                                <li value="lottery">竞彩</li>
                                <li value="first">一级</li>
                                <li value="imp">重要</li>
                            </ul>
                        </div>
                    </th>
                    <th>直播频道</th>
                    <th>状态</th>
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
                        <th colspan="10">{{date_format(date_create($time),'Y年m月d日')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$week_array[$week]}}</th>
                    </tr>
                    @foreach($match_array as $match)
                        @component('pc.cell.home_match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                    @if($bj == 0 && $loop->index == 0)
                        <?php
                        $bj = 1;
                        ?>
                        <tr class="adbanner"><td colspan="10"><a href="/download.html" target="_blank"><img src="{{env('CDN_URL')}}/img/pc/image_ad_pc.jpg"><button class="close"></button></a></td></tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <div id="News">

                <div class="title">相关文章</div>
                @foreach($arts as $index=>$art)
                <a target="_blank" href="{{$art['url']}}">{{$art['title']}}</a>
                @endforeach
                <p class="clear"></p>
            </div>
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
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/home.js?time=201808201830"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            setPage();
        }
    </script>
@endsection