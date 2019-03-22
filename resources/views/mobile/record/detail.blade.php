@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/match_list_wap_2.css?t=201903140936">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/record_list_wap_2.css?t=201903221615">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/record_wap_2.css?t=201903140936">
@endsection
@section('banner')
    <div id="Navigation">
        <a href="/" class="home"><img src="{{env('CDN_URL')}}/img/mobile/v2/logo_white.png"></a>
        <p class="inner_column_con">
            <a href="/">直播</a>
            <a href="/news/">资讯</a>
            <a href="/video/">视频</a>
            <a href="/record/" class="on">录像</a>
            <a href="/data/">数据</a>
        </p>
    </div>
@endsection
@section('content')
    <?php
    if (!is_null($match) && !is_null($sv)){
        $time = date('m月d日', $match['time']);
        $mTitle = $sv['lname'] . ' ' . $sv['hname']. ' VS '.$sv['aname'];
        $sTitle = isset($subjects[$sv['s_lid']])? $subjects[$sv['s_lid']]['name'] : '';
//        $sTitle = $sv['lname'];
    }
    else{
        $time = '';
        $mTitle = '';
        $sTitle = '';
    }
    if (isset($zhuanti))
    {
        $zt = '/'.$zhuanti['name_en'].'/record/';
    }
    else{
        $zt = '/record/';
    }
    ?>
    <h1>{{$time}} {{$mTitle}}</h1>
    <div class="link_con">
        @foreach($records as $record)
            <a href="{{$record['content']}}"><p>{{$record['title']}}</p></a>
        @endforeach
    </div>
    <p class="title_text">更多{{$sTitle}}录像</p>
    <div class="match_list_con">
        @foreach($hotRecords as $hotRecord)
            <?php
            $time = date('H:m', date_create($hotRecord['time'])->getTimestamp());
            if (!is_null($hotRecord->url)){
                $url = $hotRecord->url;
            }
            else{
                $subject = isset($subjects[$hotRecord['s_lid']])? $subjects[$hotRecord['s_lid']]['name_en'] : 'other';
                $url = \App\Http\Controllers\PC\CommonTool::getRecordDetailUrl($subject,$hotRecord['mid']);
            }
            ?>
            <a href="{{$url}}" class="football">
                <div class="team_con">
                    <p @if($hotRecord['hscore'] < $hotRecord['ascore'])class="lose"@endif><span>{{$hotRecord['hscore']}}</span>{{$hotRecord['hname']}}</p>
                    <p @if($hotRecord['hscore'] > $hotRecord['ascore'])class="lose"@endif><span>{{$hotRecord['ascore']}}</span>{{$hotRecord['aname']}}</p>
                </div>
                <div class="info_con">
                    <p>{{$hotRecord['lname']}}</p>
                    <p>{{$time}}</p>
                </div>
                <div class="status_con">查看 ></div>
            </a>
        @endforeach
    </div>
@endsection