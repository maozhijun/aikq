@extends('mobile.layout.v2.base')
@section('banner')
    <div id="Navigation">
        <div class="column_con">
            <div class="run_line">
                <?php $bj = 0?>
                @foreach($leagues as $league)
                    @if((isset($league['score']) && count($league['score']) > 0) || isset($league['west']) && count($league['west']) > 0)
                        <p class="column_item {{$bj == 0 ? 'on':''}}" forItem="{{$league['subject']}}">{{$league['league']['name']}}</p>
                        <?php
                        $bj++;
                        ?>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/match_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/data_wap_2.css">
@endsection
@section('content')
    <?php
    $bj = 0;
    ?>
    @foreach($leagues as $league)
        @if((isset($league['score']) && count($league['score']) > 0) || isset($league['west']) && count($league['west']) > 0)
            @if($league['subject'] == 'nba')
                <div class="data_list_con nba" style="{{$bj > 0 ? 'display: none;' : ''}}">
                    <div class="data_tab_con">
                        <p class="run_line">
                            <span class="on" forItem="east">东部</span>
                            <span forItem="west">西部</span>
                            {{--<span forItem="match">赛程</span>--}}
                            {{--<span forItem="matchend">赛果</span>--}}
                            <a href="/{{$league['subject']}}/">详细数据 ></a>
                        </p>
                    </div>
                    <div class="table_con east" style="">
                        <table>
                            <col width="8%"><col><col width="16%"><col width="16%"><col width="21%">
                            <tr>
                                <th></th>
                                <th>球队</th>
                                <th>胜</th>
                                <th>负</th>
                                <th>胜场差</th>
                            </tr>
                            @for($i = 0 ; $i < count($league['east']); $i++)
                                <?php
                                $item = $league['east'][$i];
                                ?>
                                <tr>
                                    <td>{{$item['rank']}}</td>
                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,$league['league']['id'],$item['tid'])}}"><img src="{{$item['ticon']}}">{{$item['tname']}}</a></td>
                                    <td>{{$item['win']}}</td>
                                    <td>{{$item['lose']}}</td>
                                    <td>{{$item['win_diff']}}</td>
                                </tr>
                            @endfor
                        </table>
                    </div>
                    <div class="table_con west" style="display: none;">
                        <table>
                            <col width="8%"><col><col width="16%"><col width="16%"><col width="21%">
                            <tr>
                                <th></th>
                                <th>球队</th>
                                <th>胜</th>
                                <th>负</th>
                                <th>胜场差</th>
                            </tr>
                            @for($i = 0 ; $i < count($league['west']); $i++)
                                <?php
                                $item = $league['west'][$i];
                                ?>
                                <tr>
                                    <td>{{$item['rank']}}</td>
                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,$league['league']['id'],$item['tid'])}}"><img src="{{$item['ticon']}}">{{$item['tname']}}</a></td>
                                    <td>{{$item['win']}}</td>
                                    <td>{{$item['lose']}}</td>
                                    <td>{{$item['win_diff']}}</td>
                                </tr>
                            @endfor
                        </table>
                    </div>
                </div>
            @elseif($league['subject'] == 'cba')
                <div class="data_list_con cba" style="{{$bj > 0 ? 'display: none;' : ''}}">
                    <div class="data_tab_con">
                        <p class="run_line">
                            <span class="on" forItem="rank">排行榜</span>
                            {{--<span forItem="goal">射手榜</span>--}}
                            {{--<span forItem="match">赛程</span>--}}
                            {{--<span forItem="matchend">赛果</span>--}}
                            <a href="/{{$league['subject']}}/">详细数据 ></a>
                        </p>
                    </div>
                    <div class="table_con rank" style="display: ;">
                        <table>
                            <col width="8%"><col><col width="16%"><col width="16%"><col width="21%">
                            <tr>
                                <th></th>
                                <th>球队</th>
                                <th>胜</th>
                                <th>负</th>
                                <th>胜场差</th>
                            </tr>
                            @for($i = 0 ; $i < count($league['score']); $i++)
                                <?php
                                $item = $league['score'][$i];
                                ?>
                                <tr>
                                    <td>{{$item['rank']}}</td>
                                    <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(2,$league['league']['id'],$item['tid'])}}"><img src="{{\App\Models\LgMatch\BasketTeam::getIcon($item['ticon'])}}">{{$item['tname']}}</a></td>
                                    <td>{{$item['win']}}</td>
                                    <td>{{$item['lose']}}</td>
                                    <td>{{$item['win_diff']}}</td>
                                </tr>
                            @endfor
                        </table>
                    </div>
                </div>
            @else
                <div class="data_list_con {{$league['subject']}}" style="{{$bj > 0 ? 'display: none;' : ''}}">
                    <div class="data_tab_con">
                        <p class="run_line">
                            <span class="on" forItem="rank">排行榜</span>
                            @if($league['league']['type'] == 1)
                                <span forItem="goal">射手榜</span>
                            @endif
                            <a href="/{{$league['subject']}}/">详细数据 ></a>
                        </p>
                    </div>
                    @if($league['league']['type'] == 2)
                        <div class="table_con rank" style="display: ;">
                            @foreach($league['score'] as $group=>$gscore)
                                <p class="match_list_date">{{$group}}组</p>
                                <table>
                                    <col width="8%"><col><col width="13%"><col width="13%"><col width="13%"><col width="16%">
                                    <tr>
                                        <th></th>
                                        <th>球队</th>
                                        <th>胜</th>
                                        <th>平</th>
                                        <th>负</th>
                                        <th>积分</th>
                                    </tr>
                                    <?php $r = 1;?>
                                    @foreach($gscore['scores'] as $item)
                                        <tr>
                                            <td>{{++$r}}</td>
                                            <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$item['lid'],$item['tid'])}}"><img src="{{$item['ticon']}}">{{$item['tname']}}</a></td>
                                            <td>{{$item['win']}}</td>
                                            <td>{{$item['draw']}}</td>
                                            <td>{{$item['lose']}}</td>
                                            <td>{{$item['score']}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endforeach
                        </div>
                    @else
                        <div class="table_con rank" style="">
                            <table>
                                <col width="8%"><col><col width="13%"><col width="13%"><col width="13%"><col width="16%">
                                <tr>
                                    <th></th>
                                    <th>球队</th>
                                    <th>胜</th>
                                    <th>平</th>
                                    <th>负</th>
                                    <th>积分</th>
                                </tr>
                                @for($i = 0 ; $i < count($league['score']); $i++)
                                    <?php
                                    $item = $league['score'][$i];
                                    ?>
                                    <tr>
                                        <td>{{$item['rank']}}</td>
                                        <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$league['league']['id'],$item['tid'])}}"><img src="{{$item['ticon']}}">{{$item['tname']}}</a></td>
                                        <td>{{$item['win']}}</td>
                                        <td>{{$item['draw']}}</td>
                                        <td>{{$item['lose']}}</td>
                                        <td>{{$item['score']}}</td>
                                    </tr>
                                @endfor
                            </table>
                        </div>
                        <div class="table_con goal player" style="display: none;">
                            <table>
                                <col width="8%"><col width="27%"><col><col width="13%">
                                <tr>
                                    <th></th>
                                    <th>球员</th>
                                    <th>球队</th>
                                    <th>进球</th>
                                </tr>
                                <?php
                                $pindex = 0;
                                $p = 'goal';
                                ?>
                                @foreach($league['playerTech'][$p] as $pkey =>$player)
                                    <?php
                                    //                                                    $player = $league['playerTech'][$p][$pkey];
                                    $pindex++;
                                    $steam = $league['playerTech']['teams'][$player['tid']];
                                    ?>
                                    <tr>
                                        <td>{{$pindex}}</td>
                                        <td>{{$player['pname']}}</td>
                                        <td><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$league['league']['id'],$player['tid'])}}"><img src="{{$steam['w_icon']}}">{{$steam['name']}}</a></td>
                                        <td>{{$player['value']}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </div>
            @endif
            <?php
            $bj++;
            ?>
        @endif
    @endforeach
@endsection
@section('bottom')
    @include("mobile.layout.v2.bottom_cell", ['cur'=>'data'])
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/data_wap_2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage()
        }
    </script>
@endsection