@extends('pc.layout.v2.base')
<?php
    $submitBD = true;//主动提交
    $bj = 0;
?>
@section('css')
<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/live_list_2.css?201903251714">
@endsection
@section('content')
<div id="Date">
    <div class="def_content">
        @foreach($matches as $time=>$match_array)
        <?php
        $mt = strtotime($time);
        $week = date('w', $mt);
        $isShowDate = false;
        ?>
        <a href="#{{date_format(date_create($time),'m_d')}}" class="date_con {{$bj == 0 ? 'on' : ''}}">{{date_format(date_create($time),'m-d')}}</a>
        @if($bj < count($matches))
            <p class="separate">/</p>
        @endif
        <?php $bj++ ?>
        @endforeach
        {{--<a href="live_match_end.html" class="type_con">完赛</a>--}}
        <a href="javascript:void(0)" class="type_con" forItem="basketball">篮球</a>
        <a href="javascript:void(0)" class="type_con" forItem="football">足球</a>
        <a href="javascript:void(0)" class="type_con on" forItem="all">全部</a>
    </div>
</div>

<div class="def_content" id="Content">

    @foreach($matches as $time=>$match_array)
        <?php
        $mt = strtotime($time);
        $week = date('w', $mt);
        $isShowDate = false;
        ?>

            <div id="{{date_format(date_create($time),'m_d')}}" class="el_con">
                <div class="header">
                    <h3><p>{{date_format(date_create($time),'m月d日')}}</p></h3>
                </div>
                <table>
                    <col width="6.8%"><col width="9.3%"><col width="6.6%"><col><col width="8.3%"><col><col width="27%"><col width="7.2%">
                    @foreach($match_array as $match)
                        @continue($match["status"] == -1) {{-- 完结的赛事不显示 --}}
                        @include('pc.cell.home_match_cell',['match'=>$match])
                    @endforeach
                </table>
            </div>
    @endforeach
</div>

@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/live_list_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
//            setADClose();
            setPage();
        }
    </script>
@endsection