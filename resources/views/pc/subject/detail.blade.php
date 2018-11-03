@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/league.css?rn={{date('YmdHi')}}">
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            <div id="Info" @if($subject["sport"] == 2) class="bk" @endif ><!--如果是篮球，增加bk类-->
                <p class="name">{{$subject["name"]}}</p>
                <div class="imgbox"><img src="{{$subject["icon"]}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/icon_teamDefault.png'"></div>
                <div class="con">{!! $subject["content"] !!}</div>
            </div>
            @if($hasLeft)
            <div class="right_part">
                @if(isset($articles) && count($articles) > 0)
                <div class="default" id="News">
                    <div class="title">
                        <p>资讯情报</p>
                    </div>
                    <ul>
                        @foreach($articles as $article)
                        <a href="{{$article["link"]}}" target="_blank" class="li">{{$article["title"]}}</a>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($ranks) && count($ranks) > 0)
                @component('pc.subject.detail_rank_cell', ['ranks'=>$ranks, 'subject'=>$subject ]) @endcomponent
                @endif
            </div>
            @endif
            <div class="left_part default" @if(!$hasLeft) style="width: 100%;" @endif >
                <div class="tab">
                    <button class="on" value="Live">直播</button>
                    @if(isset($videos) && count($videos) > 0) <button value="Video">录像</button> @endif
{{--                    @if(isset($specimens) && count($specimens) > 0) <button value="Collect">集锦</button> @endif--}}
                </div>
                <table id="Live" class="live">
                    <colgroup>
                        <col num="1" width="{{$hasRound ? '8%' : '0'}}">
                        <col num="2" width="8%">
                        <col num="3" width="8%">
                        <col num="4" width="">
                        <col num="5" width="13%">
                        <col num="6" width="">
                        <col num="7" width="25%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th><p>轮次</p></th>
                        <th>时间</th>
                        <th>状态</th>
                        <th colspan="3">对阵</th>
                        <th>直播频道</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($lives as $day=>$matches)
                    <tr>
                        <th colspan="7">{{date('Y年m月d日', $day)}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$weekCnArray[date('w', $day)]}}</th>
                    </tr>
                        @foreach($matches as $match)
                        <?php $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $subject['lid'], $match['mid']) ?>
                        <tr>
                            <td>
                                @if($hasRound)
                                    @if(isset($subject['type']) && $subject['type'] == 2)
                                        <span>{{$match['round']}}</span>
                                    @else
                                        <span>第{{$match['round']}}轮 </span>
                                    @endif
                                @endif</td>
                            <td>{{date('H:i', $match['time'])}}</td>
                            <td>@if($match['status'] > 0 && count($match['channels']) > 0)<p class="live">直播中</p>@endif</td>
                            @if(isset($match['hid']))
                                <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $subject['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
                            @else
                                <td>{{$match['hname']}}</td>
                            @endif
                            <td>@if($match['status'] == 0) VS @else {{$match['hscore'] . ' - ' . $match['ascore']}} @endif</td>
                            @if(isset($match['aid']))
                                <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $subject['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
                            @else
                                <td>{{$match['aname']}}</td>
                            @endif
                            <td>
                                @foreach($match['channels'] as $c_index=>$channel)
                                    <a target="_blank" href="{{$liveUrl}}#btn={{$c_index}}">{{$channel['name']}}</a>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
                @if(isset($videos) && count($videos) > 0)
                    @component("pc.subject.detail_video_cell", ['lives'=>$videos, 'weekCnArray'=>$weekCnArray, 'slid'=>$slid]) @endcomponent
                @endif
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/league.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage()
        }
    </script>
@endsection