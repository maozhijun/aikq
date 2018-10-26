@extends('pc.layout.base')

@section('css')
    <link rel="stylesheet" type="text/css" href="/css/pc/team.css">
@stop
<?php
    $coach = "";
?>
@section('content')
    <div id="Content">
        <div class="inner">
            @if(isset($league) && isset($league['name_en']))
                <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;>&nbsp;&nbsp;<a href="/{{$league['name_en']}}">{{$league['name']}}</a>&nbsp;&nbsp;>&nbsp;&nbsp;<span class="on">{{$team['name']}}</span></div>
            @else
                <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;>&nbsp;&nbsp;<span class="on">{{$team['name']}}</span></div>
            @endif
            <div class="right_part">
                <div id="Player">
                    <p class="title">球队球员</p>
                    <table>
                        <colgroup>
                            <col width="50">
                            <col>
                            <col width="50">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>号码</th>
                            <th>姓名</th>
                            <th>位置</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($team['lineup']) && count($team['lineup']) > 0)
                            @foreach($team['lineup'] as $lineup)
                                <?php if (str_contains($lineup['position'], "教练")) $coach = $lineup['name']; ?>
                                <tr>
                                    <td>{{strlen($lineup['num']) > 0 ? $lineup['num'] : '-'}}</td>
                                    <td>{{$lineup['name']}}</td>
                                    <td>{{$lineup['position']}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td><p>-</p></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div id="Rank">
                    @if(isset($rank) && count($rank) > 0)
                        @include('pc.team.detail_rank_cell', ['ranks'=>$rank, 'subject'=>$league ])
                    @endif
                </div>
            </div>
            <div class="left_part">
                <div id="Info">
                    <div class="imgbox">
                        <img src="{{$team['icon']}}" onerror='this.src="/img/pc/icon_teamDefault.png"'>
                        <h1>{{$team['name']}}</h1>
                    </div>
                    <div class="part partOne">
                        <p><span>现任主教练：</span>{{$coach}}</p>
                        <p><span>所在城市：</span>{{$team['city']}}</p>
                        <p><span>成立时间：</span>{{strlen($team['establish']) > 0 ? $team['establish'] : "-"}}</p>
                    </div>
                    <div class="part partTwo">
                        <p><span>外文队名：</span>@if(isset($team['nameEn']) && strlen($team['nameEn']) > 0){{$team['nameEn']}} @else - @endif</p>
                        <p><span>球队主场：</span>{{$team['gym']}}</p>
                    </div>
                </div>
                <div id="Match">
                    <p class="title">最近比赛</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像/直播</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lives) && count($lives) > 0)
                            @foreach($lives as $match)
                                <?php $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $match['mid']) ?>
                                <tr>
                                    <td>{{$match['lname']}}</td>
                                    <td>{{date('y-m-d', $match['time'])}} {{date('H:i', $match['time'])}}</td>
                                    @if(isset($match['hid']) && $match['hid'] != $team['id'])
                                        <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
                                    @else
                                        <td>{{$match['hname']}}</td>
                                    @endif
                                    @if($match['status'] < 0)
                                        <td>{{$match['hscore']}} - {{$match['ascore']}}</td>
                                    @else
                                        <td>VS</td>
                                    @endif
                                    @if(isset($match['aid']) && $match['aid'] != $team['id'])
                                        <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
                                    @else
                                        <td>{{$match['aname']}}</td>
                                    @endif
                                    <td>
                                        @if($match['status'] >= 0)
                                            @foreach($match['channels'] as $c_index=>$channel)
                                                <a target="_blank" href="{{$liveUrl}}?btn={{$c_index}}">{{$channel['name']}}</a>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($articles) && count($articles) > 0)
                    <div id="News">
                        <p class="title">相关新闻</p>
                        @foreach($articles as $article)
                            <div class="item">
                                <a target="_blank" href="{{$article['link']}}">
                                    <p class="imgbox" style="background: url({{$article['cover']}}); background-size: cover;"></p>
                                    <p class="con">{{$article['title']}}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(isset($videos) && count($videos) > 0)
                    <div id="Record">
                        <p class="title">相关录像</p>
                        <div class="item">
                            <a href="">
                                <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                                <p class="con">罗纳尔多原告的律师要求证明对方引用的文件是假的,aiyowei</p>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="clear"></div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        function initRankButton() {
            $('#Rank .title button').click(function(){
                if (!$(this).hasClass('on')) {
                    $('#Rank .title button.on').removeClass('on');
                    $(this).addClass('on');

                    $('#East, #West').css('display','none');
                    $('#' + $(this).val()).css('display','');
                }
            });
        }
        initRankButton();

        @if(isset($league))
            $(function () {
                $.ajax({
                    url: "/json/rank/{{$league['sport']}}/{{$league['lid']}}.html",
                    dataType: "html",
                    success: function (data) {
                        if(data) {
                           $("#Rank").html(data);
                        initRankButton();
                        }
                    }
                });
            });
        @endif
    </script>
@stop