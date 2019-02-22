@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/data_2.css?time=20192191536">
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$zhuanti['name']}}数据
            </div>
        </div>
    @endif
    <div class="def_content">
        <div class="rank_list">
            @foreach($leagues as $league)
                @if($league['subject'] == 'nba')
                    <div class="item_box">
                        <div class="item_con">
                            <div class="header">
                                <h3><p>NBA</p></h3>
                                <p class="aline">
                                    <a href="/{{$league['subject']}}/data/">详细NBA数据></a>
                                    <a href="/{{$league['subject']}}/">进入NBA专区></a>
                                </p>
                            </div>
                            <div class="tab_con">
                                <div class="con_in left">
                                    <div class="title">
                                        <h4>积分榜</h4>
                                        <p class="tab_item" forItem="west">西部</p>
                                        <p class="tab_item on" forItem="east">东部</p>
                                    </div>
                                    <div class="tab_box">
                                        <table class="east">
                                            <col><col width="15%"><col width="15%"><col width="25%">
                                            <tr><th>球队</th><th>胜</th><th>负</th><th>胜场差</th></tr>
                                            @for($i = 0 ; $i < min(8,count($league['east'])); $i++)
                                                <?php
                                                $item = $league['east'][$i];
                                                $steam = $league['teams'][$item['tid']];
                                                ?>
                                                <tr>
                                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,$league['league']['lid'],$item['tid'])}}">{{$item['rank']}} {{$steam['name_china_short']}}</a></td>
                                                    <td>{{$item['win']}}</td>
                                                    <td>{{$item['lose']}}</td>
                                                    <td>{{$item['win_diff']}}</td>
                                                </tr>
                                            @endfor
                                        </table>
                                        <table class="west" style="display: none;">
                                            <col><col width="15%"><col width="15%"><col width="25%">
                                            <tr><th>球队</th><th>胜</th><th>负</th><th>胜场差</th></tr>
                                            @for($i = 0 ; $i < min(8,count($league['west'])); $i++)
                                                <?php
                                                $item = $league['west'][$i];
                                                $steam = $league['teams'][$item['tid']];
                                                ?>
                                                <tr>
                                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,$league['league']['lid'],$item['tid'])}}">{{$item['rank']}} {{$steam['name_china_short']}}</a></td>
                                                    <td>{{$item['win']}}</td>
                                                    <td>{{$item['lose']}}</td>
                                                    <td>{{$item['win_diff']}}</td>
                                                </tr>
                                            @endfor
                                        </table>
                                    </div>
                                </div>
                                <div class="con_in right">
                                    <div class="title">
                                        <h4>球员数据</h4>
                                        <p class="tab_item" forItem="assist">助攻</p>
                                        <p class="tab_item" forItem="rebound">篮板</p>
                                        <p class="tab_item on" forItem="score">得分</p>
                                    </div>
                                    <div class="tab_box">
                                        <table class="score">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均得分</th></tr>
                                            <?php
                                            $pindex = 0;
                                            $p = 'ppg';
                                            ?>
                                            @foreach($league['playerTech'][$p] as $pkey =>$player)
                                                @if($pindex < 8)
                                                    <?php
                                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                                    $pindex++;
                                                    //                                                $steam = $league['teams'][$player['tid']];
                                                    ?>
                                                    <tr><td>{{$pindex}} {{$player['name']}}</td><td>{{$player[$p]}}</td></tr>
                                                @endif
                                            @endforeach
                                        </table>
                                        <table class="rebound" style="display: none;">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均篮板</th></tr>
                                            <?php
                                            $pindex = 0;
                                            $p = 'rpg';
                                            ?>
                                            @foreach($league['playerTech'][$p] as $pkey =>$player)
                                                @if($pindex < 8)
                                                    <?php
                                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                                    $pindex++;
                                                    //                                                $steam = $league['teams'][$player['tid']];
                                                    ?>
                                                    <tr><td>{{$pindex}} {{$player['name']}}</td><td>{{$player[$p]}}</td></tr>
                                                @endif
                                            @endforeach
                                        </table>
                                        <table class="assist" style="display: none;">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均助攻</th></tr>
                                            <?php
                                            $pindex = 0;
                                            $p = 'apg';
                                            ?>
                                            @foreach($league['playerTech'][$p] as $pkey =>$player)
                                                @if($pindex < 8)
                                                    <?php
                                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                                    $pindex++;
                                                    //                                                $steam = $league['teams'][$player['tid']];
                                                    ?>
                                                    <tr><td>{{$pindex}} {{$player['name']}}</td><td>{{$player[$p]}}</td></tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($league['subject'] == 'cba')
                    <div class="item_box">
                        <div class="item_con">
                            <div class="header">
                                <h3><p>CBA</p></h3>
                                <p class="aline">
                                    <a href="/{{$league['subject']}}/data/">详细CBA数据></a>
                                    <a href="/{{$league['subject']}}/">进入CBA专区></a>
                                </p>
                            </div>
                            <div class="tab_con">
                                <div class="con_in left">
                                    <div class="title">
                                        <h4>积分榜</h4>
                                    </div>
                                    <div class="tab_box">
                                        <table>
                                            <col><col width="15%"><col width="15%"><col width="25%">
                                            <tr><th>球队</th><th>胜</th><th>负</th><th>胜场差</th></tr>
                                            @for($i = 0 ; $i < min(8,count($league['score'])); $i++)
                                                <?php
                                                $item = $league['score'][$i];
                                                $steam = $league['teams'][$item['tid']];
                                                ?>
                                                <tr>
                                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,$league['league']['lid'],$item['tid'])}}">{{$item['rank']}} {{$steam['name_china_short']}}</a></td>
                                                    <td>{{$item['win']}}</td>
                                                    <td>{{$item['lose']}}</td>
                                                    <td>{{$item['win_diff']}}</td>
                                                </tr>
                                            @endfor
                                        </table>
                                    </div>
                                </div>
                                <div class="con_in right">
                                    <div class="title">
                                        <h4>球员数据</h4>
                                        <p class="tab_item" forItem="assist">助攻</p>
                                        <p class="tab_item" forItem="rebound">篮板</p>
                                        <p class="tab_item on" forItem="score">得分</p>
                                    </div>
                                    <div class="tab_box">
                                        <table class="score">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均得分</th></tr>
                                            <?php
                                            $pindex = 0;
                                            $p = 'ppg';
                                            ?>
                                            @foreach($league['playerTech'][$p] as $pkey =>$player)
                                                @if($pindex < 8)
                                                    <?php
                                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                                    $pindex++;
                                                    //                                                $steam = $league['teams'][$player['tid']];
                                                    ?>
                                                    <tr><td>{{$pindex}} {{$player['name']}}</td><td>{{$player[$p]}}</td></tr>
                                                @endif
                                            @endforeach
                                        </table>
                                        <table class="rebound" style="display: none;">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均篮板</th></tr>
                                            <?php
                                            $pindex = 0;
                                            $p = 'rpg';
                                            ?>
                                            @foreach($league['playerTech'][$p] as $pkey =>$player)
                                                @if($pindex < 8)
                                                    <?php
                                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                                    $pindex++;
                                                    //                                                $steam = $league['teams'][$player['tid']];
                                                    ?>
                                                    <tr><td>{{$pindex}} {{$player['name']}}</td><td>{{$player[$p]}}</td></tr>
                                                @endif
                                            @endforeach
                                        </table>
                                        <table class="assist" style="display: none;">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均助攻</th></tr>
                                            <?php
                                            $pindex = 0;
                                            $p = 'apg';
                                            ?>
                                            @foreach($league['playerTech'][$p] as $pkey =>$player)
                                                @if($pindex < 8)
                                                    <?php
                                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                                    $pindex++;
                                                    //                                                $steam = $league['teams'][$player['tid']];
                                                    ?>
                                                    <tr><td>{{$pindex}} {{$player['name']}}</td><td>{{$player[$p]}}</td></tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="item_box">
                        <div class="item_con">
                            <div class="header">
                                <h3><p>{{$league['league']['name']}}</p></h3>
                                <p class="aline">
                                    <a href="/{{$league['subject']}}/data/">详细{{$league['league']['name']}}数据></a>
                                    <a href="/{{$league['subject']}}/">进入{{$league['league']['name']}}专区></a>
                                </p>
                            </div>
                            <div class="tab_con">
                                <div class="con_in left">
                                    <div class="title">
                                        <h4>积分榜</h4>
                                    </div>
                                    <div class="tab_box">
                                        <table>
                                            <col><col width="12%"><col width="12%"><col width="12%"><col width="18%">
                                            <tr><th>球队</th><th>胜</th><th>平</th><th>负</th><th>积分</th></tr>
                                            @for($i = 0 ; $i < min(8,count($league['score'])); $i++)
                                                <?php
                                                $item = $league['score'][$i];
                                                $steam = $league['teams'][$item['tid']];
                                                ?>
                                                <tr>
                                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$league['league']['lid'],$item['tid'])}}">{{$item['rank']}} {{$steam['name']}}</a></td>
                                                    <td>{{$item['win']}}</td>
                                                    <td>{{$item['draw']}}</td>
                                                    <td>{{$item['lose']}}</td>
                                                    <td>{{$item['score']}}</td>
                                                </tr>
                                            @endfor
                                        </table>
                                    </div>
                                </div>
                                <div class="con_in right">
                                    <div class="title">
                                        <h4>球员数据</h4>
                                        <p class="tab_item" forItem="assist">助攻榜</p>
                                        <p class="tab_item on" forItem="score">射手榜</p>
                                    </div>
                                    <div class="tab_box">
                                        <table class="score">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>场均得分</th></tr>
                                            {{--<?php--}}
                                            {{--$pindex = 0;--}}
                                            {{--$p = 'ppg';--}}
                                            {{--?>--}}
                                            {{--@foreach($league['playerTech'][$p] as $pkey =>$player)--}}
                                            {{--@if($pindex < 8)--}}
                                            {{--<?php--}}
                                            {{--//                                                    $player = $league['playerTech'][$p][$pkey];--}}
                                            {{--$pindex++;--}}
                                            {{--//                                                $steam = $league['teams'][$player['tid']];--}}
                                            {{--?>--}}
                                            <tr>
                                                {{--<td>{{$pindex}} {{$player['name']}}</td>--}}
                                                {{--<td>{{$player[$p]}}</td>--}}
                                            </tr>
                                            {{--@endif--}}
                                            {{--@endforeach--}}
                                        </table>
                                        <table class="assist" style="display: none;">
                                            <col><col width="35%">
                                            <tr><th>球员</th><th>助攻</th></tr>
                                            <tr><td>1 詹姆斯哈登</td><td>17</td></tr>
                                            <tr><td>2 斯蒂芬·库里</td><td>14</td></tr>
                                            <tr><td>3 安东尼·戴维斯</td><td>13</td></tr>
                                            <tr><td>4 费得了</td><td>13</td></tr>
                                            <tr><td>5 波士顿人</td><td>12</td></tr>
                                            <tr><td>6 布鲁</td><td>12</td></tr>
                                            <tr><td>7 夏洛特</td><td>9</td></tr>
                                            <tr><td>8 阿密热</td><td>8</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/data_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection