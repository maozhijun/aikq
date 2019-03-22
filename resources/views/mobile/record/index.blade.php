@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/match_list_wap_2.css?t=201903140936">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/record_list_wap_2.css?t=201903140936">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>爱看球</h1>
        <div class="column_con">
            <div class="run_line">
                <p class="column_item on" foritem="all">全部</p>
                <p class="column_item" foritem="basketball">篮球</p>
                <p class="column_item" foritem="football">足球</p>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="date_con">
        <?php
        $i = 0;
        $weekday = ['周一','周二','周三','周四','周五','周六','周日'];
        ?>
        @foreach($datas as $key=>$data)
            <?php
            $month = explode('-',$key)[1];
            $day = explode('-',$key)[2];
            if ($i == 0){
                $week = '今天';
            }
            else{
                $week = $weekday[date('N',strtotime($key)) - 1];
            }
            ?>
            <div class="date_item {{$i == 0 ? 'on' :''}}" foritem="{{$month.'_'.$day}}"><p class="week">{{$week}}</p><p class="date">{{$month.'-'.$day}}</p></div>
            <?php $i++;?>
        @endforeach
        <div class="date_item other" foritem="other"><p class="date">选择日期</p><input type="date" type="date-time" name="date"></div>
    </div>
    <?php
    $i = 0;
    ?>
    @foreach($datas as $key=>$data)
        <?php
        $month = explode('-',$key)[1];
        $day = explode('-',$key)[2];
        ?>
        <div class="match_list_con {{$month.'_'.$day}}" @if($i > 0) style="display: none;"@endif>
            @foreach($data['records'] as $match)
                <?php
                $type = $match['sport'] == 1 ? 'football' : 'basketball';
                $timeStr = date('H:i',date_create($match['time'])->getTimestamp());
                $subject = isset($subjects[$match['s_lid']])? $subjects[$match['s_lid']]['name_en'] : 'other';
                if (!is_null($match->url)){
                    $url = $match->url;
                }
                else{
                    $url = \App\Http\Controllers\PC\CommonTool::getRecordDetailUrl($subject,$match['mid']);
                }
                $hurl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($match['sport'],$match['s_lid'],$match['hid']);
                $aurl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl2($match['sport'],$match['s_lid'],$match['aid']);
                ?>
                <a href="{{$url}}" class="{{$type}}">
                    <div class="team_con">
                        <p @if($match['hscore'] < $match['ascore'])class="lose"@endif><span>{{$match['hscore']}}</span>{{$match['hname']}}</p>
                        <p @if($match['ascore'] < $match['hscore'])class="lose"@endif><span>{{$match['ascore']}}</span>{{$match['aname']}}</p>
                    </div>
                    <div class="info_con">
                        <p>{{$match['lname']}}</p>
                        <p>{{$timeStr}}</p>
                    </div>
                    <div class="status_con"></div>
                </a>
            @endforeach
        </div>
        <?php $i++;?>
    @endforeach
@endsection
@section('bottom')
    @include("mobile.layout.v2.bottom_cell", ['cur'=>'record'])
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/record_list_wap_2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage()
        }
    </script>
@endsection