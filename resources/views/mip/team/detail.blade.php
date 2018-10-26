@extends('mip.layout.base')

@section('css')
    <link rel="stylesheet" type="text/css" href="/css/team.css">
@stop
<?php
    $coach = "";
    if(isset($team['lineup']) && count($team['lineup']) > 0) {
        foreach($team['lineup'] as $lineup) {
            if (str_contains($lineup['position'], "教练")) $coach = $lineup['name'];
        }
    }
?>
@section('banner')
    <div id="Navigation">
        <h1>{{$h1}}</h1>
        <div class="banner"><a class="home" href="/"></a>球队信息</div>
    </div>
@endsection

@section('content')
    <div id="Info">
        <mip-img layout="fixed" width="90" height="90" src="{{$team['icon']}}"></mip-img>
        <div class="name">{{$team['name']}}</div>
        <div class="more">
            <p><span>现任主教练：</span>{{$coach}}</p>
            <p><span>所在城市：</span>{{$team['city']}}</p>
            <p><span>成立时间：</span>{{strlen($team['establish']) > 0 ? $team['establish'] : "-"}}</p>
            <p><span>外文队名：</span>@if(isset($team['nameEn']) && strlen($team['nameEn']) > 0){{$team['nameEn']}} @else - @endif</p>
            <p><span>球队主场：</span>{{$team['gym']}}</p>
        </div>
    </div>
    <div id="Content">
        <mip-vd-tabs class="tab">
            <section>
                <li>最近比赛</li>
                <li>球队球员</li>
                <li>相关新闻</li>
                <li>相关录像</li>
            </section>
            <div id="Data" class="default">
                <table>
                    <thead>
                    <tr>
                        <th>赛事</th>
                        <th>时间</th>
                        <th>对阵</th>
                        <th>录像/直播</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($lives) && count($lives) > 0)
                        @foreach($lives as $match)
                            <?php $liveUrl = \App\Http\Controllers\Mip\UrlCommonTool::matchLiveUrl($match['lid'], $match['sport'], $match['mid']) ?>
                            <tr>
                                <td>{{$match['lname']}}</td>
                                <td><span>{{date('y/m/d', $match['time'])}}</span><br/>{{date('H:i', $match['time'])}}</td>
                                <td>
                                    @if(isset($match['hid']) && $match['hid'] != $team['id'])
                                        <a href="{{\App\Http\Controllers\Mip\UrlCommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a>
                                    @else
                                        {{$match['hname']}}
                                    @endif
                                    @if($match['status'] < 0)
                                        {{$match['hscore']}} - {{$match['ascore']}}
                                    @else
                                         vs
                                    @endif
                                    @if(isset($match['aid']) && $match['aid'] != $team['id'])
                                        <a href="{{\App\Http\Controllers\Mip\UrlCommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a>
                                    @else
                                        {{$match['aname']}}
                                    @endif
                                </td>
                                <td>
                                    @if($match['status'] >= 0)
                                        @foreach($match['channels'] as $c_index=>$channel)
                                            <a href="{{$liveUrl}}?btn={{$c_index}}">{{$channel['name']}}</a>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div id="Player" class="default">
                <table>
                    <thead>
                    <tr>
                        <th>位置</th>
                        <th>号码</th>
                        <th>姓名</th>
                        @if(isset($league) && $league['sport'] == 2)
                            <th>年薪</th>
                        @else
                            <th>估值</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($team['lineup']) && count($team['lineup']) > 0)
                        @foreach($team['lineup'] as $lineup)
                            <tr>
                                <td>{{$lineup['position']}}</td>
                                <td>@if(strlen($lineup['num']) > 0)<p>{{$lineup['num']}}</p> @else -@endif</td>
                                <td>{{$lineup['name']}}</td>
                                <td>{{strlen($lineup['value']) > 0 ? $lineup['value']."万" : "-"}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div id="News">
                @if(isset($articles) && count($articles) > 0)
                    @foreach($articles as $article)
                        <a href="{{$article['link']}}">
                            <mip-img height="80" width="120" layout="responsive" src="{{$article['cover']}}"></mip-img>
                            <p class="con">{{$article['title']}}</p>
                        </a>
                    @endforeach
                @endif
            </div>
            <div id="Record">
                @if(isset($videos) && count($videos) > 0)
                    <div class="item">
                        <a href="record.html">
                            <mip-img height="100" layout="responsive" src="https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2148120987,1371891926&fm=27&gp=0.jpg"></mip-img>
                            <p class="con">罗纳尔多原告的律师要</p>
                        </a>
                    </div>
                @endif
            </div>
        </mip-vd-tabs>
    </div>
@stop

@section('js')
    <script src="https://c.mipcdn.com/static/v1/mip-vd-tabs/mip-vd-tabs.js"></script>
@stop