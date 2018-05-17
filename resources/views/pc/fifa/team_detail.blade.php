@extends('pc.layout.base')
@section('content')
    <?php $tids = [
        //A
            '578'=>['tid'=>578,'win_tid'=>735,'name'=>'埃及','url'=>'https://www.liaogou168.com/news/WorldCup/20180516/15885.html'],
            '586'=>['tid'=>586,'win_tid'=>746,'name'=>'俄罗斯','url'=>'https://www.liaogou168.com/news/WorldCup/20180516/15892.html'],
            '607'=>['tid'=>607,'win_tid'=>767,'name'=>'乌拉圭','url'=>'https://www.liaogou168.com/news/WorldCup/20180516/15899.html'],
            '716'=>['tid'=>716,'win_tid'=>891,'name'=>'沙地阿拉伯','url'=>'https://www.liaogou168.com/news/info/20180516/15889.html'],
        //B
            '605'=>['tid'=>605,'win_tid'=>765,'name'=>'葡萄牙','url'=>'https://www.liaogou168.com/news/info/20180515/15844.html'],
            '612'=>['tid'=>612,'win_tid'=>772,'name'=>'西班牙','url'=>'https://www.liaogou168.com/news/info/20180516/15900.html'],
            '622'=>['tid'=>622,'win_tid'=>783,'name'=>'伊朗','url'=>'https://www.liaogou168.com/news/info/20180515/15845.html'],
            '646'=>['tid'=>646,'win_tid'=>813,'name'=>'摩洛哥','url'=>'https://www.liaogou168.com/news/info/20180515/15846.html'],
        //C
            '509'=>['tid'=>509,'win_tid'=>638,'name'=>'丹麦','url'=>'https://www.liaogou168.com/news/info/20180516/15891.html'],
            '518'=>['tid'=>518,'win_tid'=>649,'name'=>'法国','url'=>'https://www.liaogou168.com/news/info/20180516/15893.html'],
            '614'=>['tid'=>614,'win_tid'=>774,'name'=>'秘鲁','url'=>'https://www.liaogou168.com/news/info/20180516/15895.html'],
            '732'=>['tid'=>732,'win_tid'=>913,'name'=>'澳大利亚','url'=>'https://www.liaogou168.com/news/info/20180516/15886.html'],
        //D
            '596'=>['tid'=>596,'win_tid'=>756,'name'=>'冰岛','url'=>'https://www.liaogou168.com/news/info/20180515/15850.html'],
            '606'=>['tid'=>606,'win_tid'=>766,'name'=>'阿根廷','url'=>'https://www.liaogou168.com/news/info/20180515/15847.html'],
            '608'=>['tid'=>608,'win_tid'=>768,'name'=>'克罗地亚','url'=>'https://www.liaogou168.com/news/info/20180515/15848.html'],
            '626'=>['tid'=>626,'win_tid'=>789,'name'=>'尼日利亚','url'=>'https://www.liaogou168.com/news/info/20180515/15849.html'],
        //E
            '512'=>['tid'=>512,'win_tid'=>642,'name'=>'塞尔维亚','url'=>'https://www.liaogou168.com/news/info/20180516/15897.html'],
            '517'=>['tid'=>517,'win_tid'=>648,'name'=>'瑞士','url'=>'https://www.liaogou168.com/news/info/20180516/15896.html'],
            '618'=>['tid'=>618,'win_tid'=>778,'name'=>'巴西','url'=>'https://www.liaogou168.com/news/info/20180516/15888.html'],
            '733'=>['tid'=>733,'win_tid'=>914,'name'=>'哥斯达黎加','url'=>'https://www.liaogou168.com/news/info/20180516/15894.html'],
        //F
            '513'=>['tid'=>513,'win_tid'=>644,'name'=>'瑞典','url'=>'https://www.liaogou168.com/news/info/20180515/15854.html'],
            '519'=>['tid'=>519,'win_tid'=>650,'name'=>'德国','url'=>'https://www.liaogou168.com/news/info/20180515/15851.html'],
            '651'=>['tid'=>651,'win_tid'=>819,'name'=>'墨西哥','url'=>'https://www.liaogou168.com/news/info/20180515/15852.html'],
            '721'=>['tid'=>721,'win_tid'=>898,'name'=>'韩国','url'=>'https://www.liaogou168.com/news/info/20180515/15855.html'],
        //G
            '514'=>['tid'=>514,'win_tid'=>645,'name'=>'比利时','url'=>'https://www.liaogou168.com/news/info/20180516/15890.html'],
            '584'=>['tid'=>584,'win_tid'=>744,'name'=>'英格兰','url'=>'https://www.liaogou168.com/news/info/20180516/15901.html'],
            '634'=>['tid'=>634,'win_tid'=>798,'name'=>'巴拿马','url'=>'https://www.liaogou168.com/news/info/20180516/15887.html'],
            '653'=>['tid'=>653,'win_tid'=>823,'name'=>'突尼斯','url'=>'https://www.liaogou168.com/news/info/20180516/15898.html'],
        //H
            '508'=>['tid'=>508,'win_tid'=>637,'name'=>'波兰','url'=>'https://www.liaogou168.com/news/info/20180515/15857.html'],
            '615'=>['tid'=>615,'win_tid'=>775,'name'=>'哥伦比亚','url'=>'https://www.liaogou168.com/news/info/20180515/15856.html'],
            '648'=>['tid'=>648,'win_tid'=>815,'name'=>'塞内加尔','url'=>'https://www.liaogou168.com/news/info/20180515/15858.html'],
            '726'=>['tid'=>726,'win_tid'=>903,'name'=>'日本','url'=>'https://www.liaogou168.com/news/info/20180515/15859.html'],
    ];
    $desUrl = $tids[$team['id']]['url'];
    ?>
    <div id="Content">
        <div class="inner">
            <div id="Info">
                <div class="icon" style="background: url({{env('CDN_URL')}}/img/pc/fifa/image_bg.jpg) no-repeat center; background-size: cover;">
                    <img src="{{$team['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/fifa/icon_teamDefault.png'">
                </div>
                <div class="con">
                    <h1>{{$team['name']}}</h1>
                    <p>{{$team['describe']}}</p>
                    <a target="_blank" href="{{$desUrl}}">详细介绍&nbsp;&nbsp;>></a>
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