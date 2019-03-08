@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?201903071908">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/record_2.css?time=20192191536">
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
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/record/">{{$zhuanti['name']}}录像</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$time}}{{$mTitle}}
            </div>
        </div>
    @endif
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div id="Video_play_box">
                <h1>{{$time}} {{$mTitle}}</h1>
                <ul>
                    @foreach($records as $record)
                        <li><a target="_blank" href="{{$record['content']}}" class="go_play">【立即播放】</a><a target="_blank" href="{{$record['content']}}">{{$record['title']}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="el_con">
                <div class="header">
                    <h3><p>{{$sTitle}}录像</p></h3>
                    <p class="aline">
                        <a href="{{$zt}}">全部{{$sTitle}}录像 ></a>
                    </p>
                </div>
                <table class="match record_list">
                    <col width="25%"><col><col width="15%"><col><col width="20%">
                    @foreach($hotRecords as $hotRecord)
                        <?php
                        $time = date('Y年m月d日 H:m', date_create($hotRecord['time'])->getTimestamp());
                        $subject = isset($subjects[$hotRecord['s_lid']])? $subjects[$hotRecord['s_lid']]['name_en'] : 'other';
                        if (!is_null($hotRecord->url)){
                            $url = $hotRecord->url;
                        }
                        else{
                            $url = \App\Http\Controllers\PC\CommonTool::getRecordDetailUrl($subject,$hotRecord['mid']);
                        }
                        ?>
                        <tr>
                            <td><span>{{$time}}</span></td>
                            <td class="host">{{$hotRecord['hname']}}</td>
                            <td class="vs">{{$hotRecord['hscore']}} - {{$hotRecord['ascore']}}</td>
                            <td class="away">{{$hotRecord['aname']}}</td>
                            <td class="line"><a href="{{$url}}" class="live">观看录像</a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="el_con">
                <div class="header">
                    <h3><p>{{strlen($sTitle) == 0 ? '最新' : $sTitle}}视频</p></h3>
                    <p class="aline">
                        <a href="{{is_null($zhuanti) ? '/video/':'/'.$zhuanti['name_en'].'/video/'}}">全部{{strlen($sTitle) == 0 ? '' : $sTitle}}视频 ></a>
                    </p>
                </div>
                <div class="video_list">
                    @if(isset($comboData) && isset($comboData['videos']))
                        @foreach($comboData['videos'] as $video)
                            <div class="list_item">
                                <a href="{{$video['link']}}">
                                    <p class="img_box"><img src="{{$video['image']}}"></p>
                                    <div class="title_text"><p><span>{{$video['title']}}</span></p></div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div id="Right_part" style="">
            @if(isset($comboData) && isset($zhuanti))
                @include('pc.cell.v2.right_league_cell', ['zhuanti'=>$zhuanti])
                {{--<a class="banner_entra" href="">--}}
                    {{--<img src="http://img1.gtimg.com/sports/pics/hv1/231/116/2220/144385311.png">--}}
                    {{--<h3>圣安东尼奥马刺</h3>--}}
                    {{--<p>赛事：<span>NBA</span>排名：<span>东部第1名</span></p>--}}
                {{--</a>--}}
                {{--<a class="banner_entra" href="">--}}
                    {{--<img src="http://img1.gtimg.com/sports/pics/hv1/133/21/2268/147482188.png">--}}
                    {{--<h3>多伦多猛龙</h3>--}}
                    {{--<p>赛事：<span>NBA</span>排名：<span>东部第1名</span></p>--}}
                {{--</a>--}}
                <div class="con_box">
                    <div class="header_con">
                        <h4>最近直播</h4>
                        <a href="/">全部直播</a>
                    </div>
                    <div class="live">
                        @if(isset($comboData['matches']))
                            @foreach($comboData['matches'] as $match)
                                @include('pc.cell.v2.right_match_cell', ['match'=>$match])
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
            <div class="con_box">
                <div class="header_con">
                    <h4>{{strlen($sTitle) == 0 ? '最新' : $sTitle}}资讯</h4>
                    {{--@if(isset($zhuanti))--}}
                    <a href="{{is_null($zhuanti) ? '/news/':'/'.$zhuanti['name_en'].'/news/'}}">全部{{$sTitle}}资讯</a>
                    {{--<a href="/news/">全部资讯</a>--}}
                </div>
                <div class="news">
                    @if(isset($comboData) && isset($comboData['articles']))
                        @foreach($comboData['articles'] as $index=>$article)
                            @if($index < 2)
                                <a href="{{$article['link']}}" class="img_news">
                                    <p class="img_box"><img src="{{$article['cover']}}"></p>
                                    <h3>{{$article['title']}}</h3>
                                </a>
                            @else
                                <a href="{{$article['link']}}" class="text_new"><h4>{{$article['title']}}</h4></a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/record_2.js"></script>
    <script type="text/javascript">
        var LeagueKeyword = '{{isset($zhuanti) ? $zhuanti['name_en'] : 'all'}}';
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection