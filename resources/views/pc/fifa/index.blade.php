@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/fifa/fifa.css?time=20180517125904">
    <style>

    </style>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/fifa/fifa.js?time=2018012619310002"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage()
        }
    </script>
    <script type="text/javascript">
        function refreshMatch() {
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var strDate = date.getDate();
            if (month >= 1 && month <= 9) {
                month = "0" + month;
            }
            if (strDate >= 0 && strDate <= 9) {
                strDate = "0" + strDate;
            }
            var url = {{env('MATCH_URL')}}+"/static/schedule/"+year+month+strDate+"/1/all.json";
            $.ajax({
                "url": url,
                dataType: "jsonp",
                "success": function (json) {
                    var matches = json['matches'];
                    for(var i = 0 ; i < matches.length ; i++){
                        var match = matches[i];
                        var a = $('li#match_cell_' + match['mid']);
                        if (a && a.length > 0){
                            a.find('.vs')[0].innerHTML=match['hscore'] + ' - ' +match['ascore'];
                            //切换比赛状态
                            var status = a[0].getAttribute('m_status');
                            a[0].setAttribute('m_status',match['status']);
                            if (status != match['status']){
                                if (match['status'] > 0){
                                    a.find('.status')[0].className = 'status live';
                                    a.find('.status')[0].innerHTML = '比赛中';
                                }
                                else if(match['status'] == -1){
                                    a.find('.status')[0].className = 'status';
                                    a.find('.status')[0].innerHTML = '已结束';
                                }
                                else if(match['status'] == 0){
                                    a.find('.status')[0].className = 'status';
                                    a.find('.status')[0].innerHTML = '<a href=""><img src="{{env('CDN_URL')}}/img/pc/fifa/icon_living_n.png"></a>';
                                }else{

                                }
                            }
                        }
                    }
                    setTimeout(refreshMatch, 60000);
                },
                "error": function () {
                    setTimeout(refreshMatch, 60000);
                }
            });
        }
    </script>
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            @if(count($top['focus']) > 0)
                <div id="Focus">
                    @foreach($top['focus'] as $focus)
                        <a
                                target="_blank"
                                @if($loop->index == 0)
                                class="item on"
                                @else
                                class="item"
                                @endif
                                href="{{$focus['url']}}" style="background: url({{$focus['cover']}}) no-repeat center; background-size: cover;">
                            <p>{{$focus['title']}}</p>
                        </a>
                    @endforeach
                </div>
            @endif
            <div id="News">
                <div class="right">
                    <div class="tab">
                        <p class="item on" for="goal">射手榜</p>
                        {{--<p class="item" for="assists">助攻榜</p>--}}
                    </div>
                    <ul class="goal">
                        @for($i = 3 ; $i < count($rank['total']); $i++)
                            <?php
                            $item = $rank['total'][$i];
                            ?>

                            <li>
                                <p>{{$i + 1}}</p>
                                <span>{{$item['goal']}}</span>
                                {{$item['name']}}
                            </li>
                        @endfor
                    </ul>
                </div>
                <div class="left">
                    <div class="title">世界杯头条<a href="https://www.liaogou168.com/news/WorldCup">世界杯资讯>></a></div>
                    <div class="con">
                        <?php
                        $topics = $top['topics'];
                        //加粗
                        $topicsF = array();
                        //普通
                        $topicsS = array();
                        for ($i = 0 ; $i < count($topics) ; $i++){
                            if($i < 3){
                                $topicsF[] = $topics[$i];
                            }
                            else{
                                $topicsS[] = $topics[$i];
                            }
                        }
                        ?>
                        <dl>
                            @for($i = 0 ; $i < 3 ;$i++)
                                @if(count($topicsF) > $i)<dt><a target="_blank" href="{{$topicsF[$i]['link']}}">{{$topicsF[$i]['title']}}</a></dt>@endif
                                <dd>@if(count($topicsS) > $i*4 + 0)<a target="_blank" href="{{$topicsS[$i*4 + 0]['link']}}">{{mb_substr($topicsS[$i*4 + 0]['title'],0,min(11,mb_strlen($topicsS[$i*4 + 0]['title'])))}}</a>@endif @if(count($topicsS) > $i*4 + 1)&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="{{$topicsS[$i*4 + 1]['link']}}">{{mb_substr($topicsS[$i*4 + 1]['title'],0,min(11,mb_strlen($topicsS[$i*4 + 1]['title'])))}}</a>@endif @if(count($topicsS) > $i*4 + 2)<a target="_blank" href="{{$topicsS[$i*4 + 2]['link']}}">{{mb_substr($topicsS[$i*4 + 2]['title'],0,min(11,mb_strlen($topicsS[$i*4 + 2]['title'])))}}</a>@endif @if(count($topicsS) > $i*4 + 3)&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="{{$topicsS[$i*4 + 3]['link']}}">{{mb_substr($topicsS[$i*4 + 3]['title'],0,min(11,mb_strlen($topicsS[$i*4 + 3]['title'])))}}</a>@endif</dd>
                            @endfor
                        </dl>
                        <div class="imgList">
                            <ul>
                                @foreach($top['videos'] as $video)
                                    <?php
                                    $videoUrl = env('APP_URL').'/live/subject/specimen/'.substr($video['id'],0,2).'/'.substr($video['id'],2,2).'/'.$video['id'].'.html'
                                    ?>
                                    <a target="_blank" class="li" href="{{$videoUrl}}">
                                        <div class="imgBox" style="background: url({{isset($video['cover'])?$video['cover']:env('CDN_URL').'/img/pc/fifa/image_bg.jpg'}}) no-repeat center; background-size: cover"></div>
                                        <p>{{$video['title']}}</p>
                                    </a>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($top['focus_matches']) > 0)
                <div id="Match">
                    <div class="title">焦点对决</div>
                    <div class="con">
                        @foreach($top['focus_matches'] as $match)
                            <?php
                            $status = $match['status'];
                            $liveUrl = isset($match['mid']) ? \App\Http\Controllers\PC\CommonTool::matchWapLivePathWithId($match['mid']) : '';
                            $matchUrl = isset($match['mid']) ? \App\Http\Controllers\PC\CommonTool::matchWapPathWithId($match['mid'],1): '';
                            ?>
                            <a target="_blank" href="{{$status > 0 ? $liveUrl : $matchUrl}}">
                                <div class="time"><span>{{date('m.d',$match['time'])}}&nbsp;&nbsp;{{date('H:i',$match['time'])}}&nbsp;&nbsp;{{$match['group']}}组</span></div>
                                <div class="item">
                                    <img src="{{$match['hicon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'" class="host">
                                    <img src="{{$match['aicon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'" class="away">
                                    <p>{{$match['hname']}}</p>
                                    @if($status == 0)
                                        <p class="vs">VS</p>
                                    @else
                                        <p class="vs">{{$match['hscore']}} : {{$match['ascore']}}</p>
                                    @endif
                                    <p>{{$match['aname']}}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            <?php
            //整理数据
            ?>
            @if(isset($dieQuit) && isset($dieQuit['16']) && count($dieQuit['16']) > 0)
                <div id="DieOut">
                    <div class="title">淘汰赛赛程</div>
                    <div class="con">
                        <!--线-->
                        <div class="line horizontal" style="width: 50%; top: 50%; left: 25%; margin-top: -1px;"></div>
                        <div class="line vertical" style="height: 50%; top: 25%; left: 50%; margin-left: -1px;"></div>
                        <div class="line left" style="height: 240px; width: 30%; top: 50%; left: 1.8%; margin: -120px 0 0 10px;"></div>
                        <div class="line right" style="height: 240px; width: 30%; top: 50%; right: 1.8%; margin: -120px 10px 0 0;"></div>
                        <div class="line left" style="height: 120px; width: 14.6%; top: 90px; left: 17px;"></div>
                        <div class="line left" style="height: 120px; width: 14.6%; bottom: 90px; left: 17px;"></div>
                        <div class="line right" style="height: 120px; width: 14.6%; top: 90px; right: 17px;"></div>
                        <div class="line right" style="height: 120px; width: 14.6%; bottom: 90px; right: 17px;"></div>
                        <!--16强-->
                        <?php
                        $posCss = array(
                        'top: 50px; left: 7px;',
                        'top: 170px; left: 7px;',
                        'bottom: 170px; left: 7px;',
                        'bottom: 50px; left: 7px;',
                        'top: 50px; right: 7px;',
                        'top: 170px; right: 7px;',
                        'bottom: 170px; right: 7px;',
                        'bottom: 50px; right: 7px;'
                        )
                        ?>
                        @foreach($dieQuit['16'] as $item)
                            <?php
                            $match = isset($item['match'])?$item['match']:null;
                            $status = 0;
                            if(isset($match)){
                            $status = $match['status'];
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
                            $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($match['mid'],1,$match['time']);
                            }
                            ?>
                            <a target="_blank" href="{{$status > 0 ? $liveUrl :$matchUrl}}" class="match" style="{{$posCss[$loop->index]}}">
                                @if(isset($item['hteam']))
                                    <p @if($status == -1 && $match['hscore'] > $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['hteam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['hscore']}}</span>
                                        @endif
                                        {{$item['hteam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                                @if(isset($item['ateam']))
                                    <p @if($status == -1 && $match['hscore'] < $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['ateam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['ascore']}}</span>
                                        @endif
                                        {{$item['ateam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                            </a>
                    @endforeach
                    <!--8强-->
                        <?php
                        $posCss = array(
                        'top: 115px; left: 17.2%;',
                        'bottom: 115px; left: 17.2%;',
                        'top: 115px; right: 17.2%;',
                        'bottom: 115px; right: 17.2%;',
                        )
                        ?>
                        @foreach($dieQuit['8'] as $item)
                            <?php
                            $match = isset($item['match'])?$item['match']:null;
                            $status = 0;
                            if(isset($match)){
                            $status = $match['status'];
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
                            $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($match['mid'],1,$match['time']);
                            }
                            ?>
                            <a target="_blank" href="{{$status > 0 ? $liveUrl :$matchUrl}}" class="match" style="{{$posCss[$loop->index]}}">
                                @if(isset($item['hteam']))
                                    <p @if($status == -1 && $match['hscore'] > $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['hteam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['hscore']}}</span>
                                        @endif
                                        {{$item['hteam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                                @if(isset($item['ateam']))
                                    <p @if($status == -1 && $match['hscore'] < $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['ateam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['ascore']}}</span>
                                        @endif
                                        {{$item['ateam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                            </a>
                        @endforeach
                        <!--准决赛-->
                        <?php
                        $posCss = array(
                        'top: 50%; left: 34%; margin-top: -40px;',
                        'top: 50%; right: 34%; margin-top: -40px;',
                        )
                        ?>
                        @foreach($dieQuit['4'] as $item)
                            <?php
                            $match = isset($item['match'])?$item['match']:null;
                            $status = 0;
                            if(isset($match)){
                            $status = $match['status'];
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
                            $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($match['mid'],1,$match['time']);
                            }
                            ?>
                            <a target="_blank" href="{{$status > 0 ? $liveUrl :$matchUrl}}" class="match" style="{{$posCss[$loop->index]}}">
                                @if(isset($item['hteam']))
                                    <p @if($status == -1 && $match['hscore'] > $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['hteam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['hscore']}}</span>
                                        @endif
                                        {{$item['hteam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                                @if(isset($item['ateam']))
                                    <p @if($status == -1 && $match['hscore'] < $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['ateam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['ascore']}}</span>
                                        @endif
                                        {{$item['ateam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                            </a>
                        @endforeach
                        <!--三四名-->
                        <?php
                        $posCss = array(
                        'bottom: 98px; left: 50%; margin-left: -7.3%;',
                        )
                        ?>
                        @foreach($dieQuit['2'] as $item)
                            <?php
                            $match = isset($item['match'])?$item['match']:null;
                            $status = 0;
                            if(isset($match)){
                            $status = $match['status'];
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
                            $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($match['mid'],1,$match['time']);
                            }
                            ?>
                            <a target="_blank" href="{{$status > 0 ? $liveUrl :$matchUrl}}" class="match" style="{{$posCss[$loop->index]}}">
                                @if(isset($item['hteam']))
                                    <p @if($status == -1 && $match['hscore'] > $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['hteam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['hscore']}}</span>
                                        @endif
                                        {{$item['hteam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                                @if(isset($item['ateam']))
                                    <p @if($status == -1 && $match['hscore'] < $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['ateam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['ascore']}}</span>
                                        @endif
                                        {{$item['ateam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                            </a>
                        @endforeach
                        <!--决赛-->
                        @foreach($dieQuit['2'] as $item)
                            <?php
                            $match = isset($item['match'])?$item['match']:null;
                            $status = 0;
                            if(isset($match)){
                            $status = $match['status'];
                            $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
                            $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($match['mid'],1,$match['time']);
                            }
                            ?>
                            <a target="_blank" href="{{$status > 0 ? $liveUrl :$matchUrl}}" class="match finals">
                                @if(isset($item['hteam']))
                                    <p @if($status == -1 && $match['hscore'] > $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['hteam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['hscore']}}</span>
                                        @endif
                                        {{$item['hteam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                                @if(isset($item['ateam']))
                                    <p @if($status == -1 && $match['hscore'] < $match['ascore']) class="team win" @else class="team" @endif>
                                        <img src="{{$item['ateam']['icon']}}" onerror="this.src = '{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                                        @if($status == 0)
                                            <span>0</span>
                                        @else
                                            <span>{{$match['ascore']}}</span>
                                        @endif
                                        {{$item['ateam']['name']}}
                                    </p>
                                @else
                                    <p class="team empty"></p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            <?php
            if(isset($schedule) && isset($schedule['stages']) && count($schedule['stages']) > 0 && isset($schedule['stages'][0]['groupMatch'])){
            $groups = $schedule['stages'][0]['groupMatch'];
            }
            else{
            $groups = array();
            }
            ?>
            <div id="Group">
                <div class="tab" site="A">
                    @foreach($groups as $key=>$data)
                        <p for="{{$key}}_Group">{{$key}}组</p>
                    @endforeach
                </div>
                <div class="tab simulation" site="A">
                    @foreach($groups as $key=>$data)
                        <p for="{{$key}}_Group">{{$key}}组</p>
                    @endforeach
                </div>
                @foreach($groups as $key=>$data)
                    <div class="group" id="{{$key}}_Group">
                        <div class="title">{{$key}}组</div>
                        <div class="right">
                            <table>
                                <colgroup>
                                    <col width="40px"><col width="90px">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>排名</th>
                                    <th>球队</th>
                                    <th>总</th>
                                    <th>胜</th>
                                    <th>平</th>
                                    <th>负</th>
                                    <th>净</th>
                                    <th>积</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['scores'] as $score)
                                    <tr>
                                        <td><p>{{$loop->index + 1}}</p></td>
                                        <td>{{$score['tname']}}</td>
                                        <td>{{$score['count']}}</td>
                                        <td>{{$score['win']}}</td>
                                        <td>{{$score['draw']}}</td>
                                        <td>{{$score['lose']}}</td>
                                        <td>{{$score['goal'] - $score['fumble']}}</td>
                                        <td>{{$score['score']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="left">
                            <ul class="match">
                                @foreach($data['matches'] as $match)
                                    <?php
                                    $status = $match['status'];
                                    $liveUrl = \App\Http\Controllers\PC\CommonTool::matchLivePathWithId($match['mid']);
                                    $matchUrl = \App\Http\Controllers\PC\CommonTool::matchPathWithId($match['mid'],1,$match['time']);
                                    ?>
                                    <li id="match_cell_{{$match['mid']}}" m_status="{{$match['status']}}">
                                        <p class="time">{{date('m.d',$match['time'])}}<br/>{{date('H:i',$match['time'])}}</p>
                                        <p class="team">{{$match['hname']}}</p>
                                        @if($status == 0)
                                            <p class="vs">VS</p>
                                        @else
                                            <p class="vs">{{$match['hscore']}} - {{$match['ascore']}}</p>
                                        @endif
                                        <p class="team">{{$match['aname']}}</p>
                                        @if($status == -1)
                                            <p class="status">已结束</p>
                                        @elseif($status == 0)
                                            <p class="status"><a href="{{$liveUrl}}" target="_blank"><img src="{{env('CDN_URL')}}/img/pc/fifa/icon_living_n.png"></a></p>
                                        @elseif($status > 0)
                                            <p class="status live">比赛中</p>
                                        @endif
                                        <p class="abox">
                                            <a target="_blank" href="{{$matchUrl}}">析</a>
                                            <a target="_blank" href="{{$matchUrl.'#Article'}}">荐</a>
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                            <ul class="team">
                                @foreach($data['scores'] as $score)
                                    <a href="/worldcup/2018/team/{{$score['tid']}}.html" target="_blank" class="li">
                                        <div class="imgbox" style="background: url({{isset($score['bg_img'])?$score['bg_img']:env('CDN_URL').'/img/pc/fifa/image_bg.jpg'}}) no-repeat center; background-size: cover;"></div>
                                        <img src="{{isset($score['ticon'])?$score['ticon']:env('CDN_URL').'/img/pc/fifa/icon_teamDefault.png'}}" class="icon">
                                        <p>{{$score['tname']}}</p>
                                    </a>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
@endsection