@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/data_2.css?time=20192191536">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/data_league_2.css?time=20192191536">
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$zhuanti['name']}}数据
            </div>
        </div>
    @endif
    <div class="def_content" id="Content">
        <div class="tab_con">
            <p class="tab_item on" forItem="0">排名</p>
            @foreach($tabs as $index=>$item)
                <p class="tab_item" forItem="{{$index+1}}">{{$item['name']}}</p>
            @endforeach
        </div>
        <div class="con_inner" style="display: ;">
            @if(isset($scores))
                @if(isset($scores['east']))
                    <h3>东部</h3>
                    <table>
                        <col width="6.25%"><col width=""><col width="7.5%"><col width="7.5%"><col width="10%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="10%">
                        <tr><th>排名</th><th>球队</th><th>胜</th><th>负</th><th>胜场差</th><th>胜率</th><th>主场</th><th>客场</th><th>得分</th><th>失分</th><th>净胜</th><th>连胜/负</th></tr>
                        @foreach($scores['east'] as $item)
                            <?php
                            $steam = $teams[$item['tid']];
                            ?>
                            <tr>
                                <td>{{$item['rank']}}</td>
                                <td><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$steam['name_china_short']}}</td>
                                <td>{{$item['win']}}</td>
                                <td>{{$item['lose']}}</td>
                                <td>{{$item['win_diff']}}</td>
                                <td>{{number_format($item['win']*100/$item['count'],1)}}%</td>
                                <td>{{$item['home_bat_w']}}-{{$item['home_bat_l']}}</td>
                                <td>{{$item['away_bat_w']}}-{{$item['away_bat_l']}}</td>
                                <td>{{$item['goal']}}</td>
                                <td>{{$item['fumble']}}</td>
                                <td>{{$item['win'] - $item['lose']}}</td>
                                @if($item['win_status'] >= 0)
                                <td>{{$item['win_status']}}连胜</td>
                                    @else
                                    <td>{{-$item['win_status']}}连败</td>
                                    @endif
                            </tr>
                        @endforeach
                    </table>
                @endif
                @if(isset($scores['west']))
                    <h3>西部</h3>
                    <table>
                        <col width="6.25%"><col width=""><col width="7.5%"><col width="7.5%"><col width="10%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="10%">
                        <tr><th>排名</th><th>球队</th><th>胜</th><th>负</th><th>胜场差</th><th>胜率</th><th>主场</th><th>客场</th><th>得分</th><th>失分</th><th>净胜</th><th>连胜/负</th></tr>
                        @foreach($scores['west'] as $item)
                            <?php
                            $steam = $teams[$item['tid']];
                            ?>
                            <tr>
                                <td>{{$item['rank']}}</td>
                                <td><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$steam['name_china_short']}}</td>
                                <td>{{$item['win']}}</td>
                                <td>{{$item['lose']}}</td>
                                <td>{{$item['win_diff']}}</td>
                                <td>{{number_format($item['win']*100/$item['count'],1)}}%</td>
                                <td>{{$item['home_bat_w']}}-{{$item['home_bat_l']}}</td>
                                <td>{{$item['away_bat_w']}}-{{$item['away_bat_l']}}</td>
                                <td>{{$item['goal']}}</td>
                                <td>{{$item['fumble']}}</td>
                                <td>{{$item['win']-$item['lose']}}</td>
                                @if($item['win_status'] >= 0)
                                    <td>{{$item['win_status']}}连胜</td>
                                @else
                                    <td>{{-$item['win_status']}}连败</td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif
                    @if(isset($scores['score']))
                        <table>
                            <col width="6.25%"><col width=""><col width="7.5%"><col width="7.5%"><col width="10%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="10%">
                            <tr><th>排名</th><th>球队</th><th>胜</th><th>负</th><th>胜场差</th><th>胜率</th><th>主场</th><th>客场</th><th>得分</th><th>失分</th><th>净胜</th><th>连胜/负</th></tr>
                            @foreach($scores['score'] as $item)
                                <?php
                                $steam = $teams[$item['tid']];
                                ?>
                                <tr>
                                    <td>{{$item['rank']}}</td>
                                    <td><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$steam['name_china_short']}}</td>
                                    <td>{{$item['win']}}</td>
                                    <td>{{$item['lose']}}</td>
                                    <td>{{$item['win_diff']}}</td>
                                    <td>{{number_format($item['win']*100/$item['count'],1)}}%</td>
                                    <td>{{$item['home_bat_w']}}-{{$item['home_bat_l']}}</td>
                                    <td>{{$item['away_bat_w']}}-{{$item['away_bat_l']}}</td>
                                    <td>{{$item['goal']}}</td>
                                    <td>{{$item['fumble']}}</td>
                                    <td>{{$item['lose']}}</td>
                                    <td>{{$item['ten_bat_w']}}连胜</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
            @endif
        </div>
        @foreach($tabs as $item)
            <div class="con_inner" style="display: none;">
                <div class="left_part">
                    <div class="part_inner">
                        <h3>球队</h3>
                        <table>
                            <col width="23%"><col width=""><col width="30%">
                            <tr><th>排名</th><th>球队</th><th>场均</th></tr>
                            @if(isset($teamTech[$item['key']]))
                                <?php $index = 1;
                                ?>
                                @foreach($teamTech[$item['key']] as $key=>$value)
                                    <?php
                                    $steam = $teams[$key];
                                    ?>
                                    <tr>
                                        <td>{{$index++}}</td>
                                        <td><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$steam['name_china_short']}}</td>
                                        <td>{{$value['value']}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
                <div class="right_part">
                    <div class="part_inner">
                        <h3>球员</h3>
                        <table>
                            <col width="16%"><col width=""><col width="30%"><col width="21%">
                            <tr><th>排名</th><th>球员</th><th>球队</th><th>场均</th></tr>
                            <?php
                            $bj = ($item['key'] == 'topg' ? 'mpg' : $item['key']);
                            ?>
                            @if(isset($playerTech[$bj]))
                                @foreach($playerTech[$bj] as $key=>$value)
                                    <?php
                                    $steam = $teams[$value['tid']];
                                    ?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$value['name']}}</td>
                                        <td><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($steam['icon'])}}">{{$steam['name_china_short']}}</td>
                                        <td>{{$value[$bj]}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/data_league_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection