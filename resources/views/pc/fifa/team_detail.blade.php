@extends('pc.layout.base')
@section('content')
    <div id="Content">
        <div class="inner">
            <div id="Info">
                <div class="icon" style="background: url({{env('CDN_URL')}}/img/pc/fifa/image_bg.jpg) no-repeat center; background-size: cover;">
                    <img src="{{$team['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                </div>
                <div class="con">
                    <h1>{{$team['name']}}</h1>
                    <p>{{$team['describe']}}</p>
                    <a href="">详细介绍&nbsp;&nbsp;>></a>
                </div>
            </div>
            <div class="right">
                <div id="Player">
                    <div class="title">教练球员</div>
                    <div class="tab">
                        <p class="item" for="coach">教练</p>
                        <p class="item on" for="fw">前锋</p>
                        <p class="item" for="md">中场</p>
                        <p class="item" for="df">后卫</p>
                        <p class="item" for="gk">门将</p>
                    </div>
                    <?php
                    $coach = array();
                    foreach ($lineup['coach'] as $player){
                        if (is_null($player['icon']))
                            $player['icon'] = '';
                        $coach[] = $player;
                    }
                    $fw = array();
                    $md = array();
                    $df = array();
                    $gk = array();
                        foreach ($lineup['lineup'] as $player){
                            if (is_null($player['icon']))
                                $player['icon'] = '';
                            if ($player['pos'] == '后卫'){
                                $df[] = $player;
                            }
                            if ($player['pos'] == '中场'){
                                $md[] = $player;
                            }
                            if ($player['pos'] == '前锋'){
                                $fw[] = $player;
                            }
                            if ($player['pos'] == '守门员'){
                                $gk[] = $player;
                            }
                        }
                    ?>
                    <table>
                        <colgroup>
                            <col width="78px">
                            <col width="60px">
                        </colgroup>
                        <tbody class="coach" style="display: none;">
                            @foreach($coach as $item)
                            <tr>
                                <td><p>教</p></td>
                                <td><img src="{{$item['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/image_player_n.jpg'"></td>
                                <td>{{$item['name']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tbody class="fw">
                            @foreach($fw as $item)
                                <tr>
                                <td><p>{{isset($item['num'])?$item['num']:''}}</p></td>
                                <td><img src="{{$item['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/image_player_n.jpg'"></td>
                                <td>{{$item['name']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="md" style="display: none;">
                        @foreach($md as $item)
                            <tr>
                                <td><p>{{isset($item['num'])?$item['num']:''}}</p></td>
                                <td><img src="{{$item['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/image_player_n.jpg'"></td>
                                <td>{{$item['name']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tbody class="df" style="display: none;">
                        @foreach($df as $item)
                            <tr>
                                <td><p>{{isset($item['num'])?$item['num']:''}}</p></td>
                                <td><img src="{{$item['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/image_player_n.jpg'"></td>
                                <td>{{$item['name']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tbody class="gk" style="display: none;">
                        @foreach($gk as $item)
                            <tr>
                                <td><p>{{isset($item['num'])?$item['num']:''}}</p></td>
                                <td><img src="{{$item['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/image_player_n.jpg'"></td>
                                <td>{{$item['name']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="left">
                <div id="Group">
                    <div class="title">小组积分</div>
                    <div class="con">
                        <table>
                            <colgroup>
                                <col width="11.5%">
                                <col width="60px">
                                <col width="30%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>排名</th>
                                <th></th>
                                <th>球队</th>
                                <th>总</th>
                                <th>胜</th>
                                <th>平</th>
                                <th>负</th>
                                <th>净</th>
                                <th>积分</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($scores as $score)
                                <tr>
                                    <td><p>{{$loop->index + 1}}</p></td>
                                    <td><img src="{{$score['ticon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'"></td>
                                    <td class="name">{{$score['tname']}}</td>
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
                </div>
                <?php
                //比赛状态分类
                $notStart = array();
                $end = array();
                foreach ($matches as $match){
                    if ($match['status'] == 0){
                        $notStart[] = $match;
                    }
                    elseif ($match['status'] == -1){
                        $end[] = $match;
                    }
                    else{
                        $notStart[] = $match;
                    }
                }
                ?>
                <div class="match">
                    <div class="title">未来赛程</div>
                    <div class="con">
                        <table>
                            <colgroup>
                                <col num="1" width="56px">
                                <col num="2" width="">
                                <col num="3" width="40px">
                                <col num="4" width="">
                                <col num="5" width="8.8%">
                                <col num="6" width="110px">
                                <col num="7" width="150px">
                            </colgroup>
                            @foreach($notStart as $match)
                                @component('pc.fifa.match_cell',['match'=>$match])
                                @endcomponent
                                @endforeach
                        </table>
                    </div>
                </div>
                <div class="match">
                    <div class="title">完场赛果</div>
                    <div class="con">
                        <table>
                            <colgroup>
                                <col num="1" width="56px">
                                <col num="2" width="">
                                <col num="3" width="40px">
                                <col num="4" width="">
                                <col num="5" width="8.8%">
                                <col num="6" width="110px">
                                <col num="7" width="150px">
                            </colgroup>
                            @foreach($end as $match)
                                @component('pc.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/fifa/team.css?time=20180203030004">
    <style>

    </style>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/fifa/team.js?time=2018012619310002"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage()
        }
    </script>
@endsection