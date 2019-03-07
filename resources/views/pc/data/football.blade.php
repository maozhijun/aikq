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
        <div class="league_player">
            <p class="on" forItem="type_league">球队数据</p>
            <p forItem="type_player">球员数据</p>
        </div>
        <div class="tab_con type_league" style="display: ">
            {{--            @if($season[''])--}}
            @if($league['type'] == 1)
                <p class="tab_item on" forItem="0">积分</p>
            @else
                <p class="tab_item on" forItem="0">小组积分</p>
            @endif
            <?php
            $index = 1;
            ?>
            @foreach($teamTabs as $item)
                <p class="tab_item" forItem="{{$index++}}">{{$item['name']}}</p>
            @endforeach
        </div>
        <div class="tab_con type_player" style="display: none;">
            <?php
            $first = 1;
            ?>
            @foreach($playerTabs as $item)
                <p class="tab_item {{$first == 1 ? 'on':''}}" forItem="{{$index++}}">{{$item['name']}}</p>
                <?php
                $first = 0;
                ?>
            @endforeach
        </div>
        <div class="con_inner" style="display: ;">
            @if(isset($scores))
                @if(isset($scores['score']))
                    @if($league['type'] == 1)
                        <table>
                            <col width="6.25%"><col width=""><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%">
                            <tr><th>排名</th><th>球队</th><th>场次</th><th>胜</th><th>平</th><th>负</th><th>进球</th><th>失球</th><th>净胜球</th><th>积分</th></tr>
                            @foreach($scores['score'] as $item)
                                @if(isset($teams[$item['tid']]))
                                    <?php
                                    $steam = $teams[$item['tid']];
                                    ?>
                                    <tr>
                                        <td>{{$item['rank']}}</td>
                                        <td>
                                            <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$zhuanti['lid'],$item['tid'])}}">
                                            <img src="{{\App\Models\LgMatch\Team::getIcon($steam['icon'])}}">{{$steam['name']}}
                                        </a>
                                        </td>
                                        <td>{{$item['count']}}</td>
                                        <td>{{$item['win']}}</td>
                                        <td>{{$item['draw']}}</td>
                                        <td>{{$item['lose']}}</td>
                                        <td>{{$item['goal']}}</td>
                                        <td>{{$item['fumble']}}</td>
                                        <td>{{$item['goal'] - $item['fumble']}}</td>
                                        <td>{{$item['score']}}</td>
                                        {{--<td>{{$item['ten_bat_w']}}连胜</td>--}}
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    @elseif($league['type'] == 2)
                        @foreach($scores['score'] as $groupKey=>$group)
                            <h3>{{$groupKey}}组</h3>
                            <table>
                                <col width="6.25%"><col width=""><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%"><col width="7.5%">
                                <tr><th>排名</th><th>球队</th><th>场次</th><th>胜</th><th>平</th><th>负</th><th>进球</th><th>失球</th><th>净胜球</th><th>积分</th></tr>
                                @foreach($group as $item)
                                    @if(isset($teams[$item['tid']]))
                                        <?php
                                        $steam = $teams[$item['tid']];
                                        ?>
                                        <tr>
                                            <td>{{$item['rank']}}</td>
                                            <td>
                                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$zhuanti['lid'],$steam['id'])}}">
                                                    <img src="{{\App\Models\LgMatch\Team::getIcon($steam['icon'])}}">{{$steam['name']}}
                                                </a>
                                            </td>
                                            <td>{{$item['count']}}</td>
                                            <td>{{$item['win']}}</td>
                                            <td>{{$item['draw']}}</td>
                                            <td>{{$item['lose']}}</td>
                                            <td>{{$item['goal']}}</td>
                                            <td>{{$item['fumble']}}</td>
                                            <td>{{$item['goal'] - $item['fumble']}}</td>
                                            <td>{{$item['score']}}</td>
                                            {{--<td>{{$item['ten_bat_w']}}连胜</td>--}}
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        @endforeach
                    @endif
                @endif
            @endif
        </div>
        @foreach($teamTabs as $item)
            <?php
            $t = ($item['key'] == 'yellow') ? '场均' : '总数';
            ?>
            <div class="con_inner" style="display: none;">
                <div class="right_part">
                    <div class="part_inner">
                        <h3>球队</h3>
                        <table>
                            <col width="23%"><col width=""><col width="30%">
                            <tr><th>排名</th><th>球队</th><th>{{$t}}</th></tr>
                            @if(isset($teamTech[$item['key']]))
                                <?php $index = 1;
                                ?>
                                @foreach($teamTech[$item['key']] as $key=>$value)
                                    @if(isset($teams[$value['id']]))
                                        <?php
                                        $steam = $teams[$value['id']];
                                        $v = $value['value'];
                                        if ($t == '场均' && isset($value['count']) && $value['count'] > 0){
                                            $v = number_format($value['value']/$value['count'],1);
                                        }
                                        ?>
                                        <tr>
                                            <td>{{$index++}}</td>
                                            <td>
                                                <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$zhuanti['lid'],$steam['id'])}}">
                                                <img src="{{\App\Models\LgMatch\Team::getIcon($steam['icon'])}}">{{$steam['name']}}
                                                </a>
                                            </td>
                                            <td>{{$v}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        @foreach($playerTabs as $item)
            @if($item['key'] == 'goal')
                <div class="con_inner" style="display: none;">
                    <div class="player_part">
                        <div class="part_inner">
                            <table>
                                <col width="12%"><col width=""><col width="24%"><col width="18%"><col width="18%">
                                <tr><th>排名</th><th>球员</th><th>球队</th><th>进球</th><th>点球</th></tr>
                                @foreach($playerTech[$item['key']] as $index=>$d)
                                    <?php
                                    $steam = $playerTech['teams'][$d['tid']];
                                    ?>
                                    <tr>
                                        <td>{{$index+1}}</td><td>{{$d['pname']}}</td>
                                        <td>
                                            <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$zhuanti['lid'],$d['tid'])}}">
                                            <img onerror="this.src ='{{$steam['w_icon']}}'" src="{{\App\Models\LgMatch\Team::getLgIcon($steam['icon'])}}">{{$steam['name']}}
                                            </a>
                                        </td>
                                        <td>{{$d['value']}}</td><td>{{$d['penalty']}}</td></tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="con_inner" style="display: none;">
                    <div class="player_part">
                        <div class="part_inner">
                            <table>
                                <col width="12%"><col width=""><col width="28%"><col width="24%">
                                <tr><th>排名</th><th>球员</th><th>球队</th><th>{{$item['name']}}</th></tr>
                                @foreach($playerTech[$item['key']] as $index=>$d)
                                    <?php
                                    $steam = $playerTech['teams'][$d['tid']];
                                    ?>
                                    <tr><td>{{$index+1}}</td><td>{{$d['pname']}}</td><td>
                                            <a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl(1,$zhuanti['lid'],$d['tid'])}}">
                                            <img onerror="this.src ='{{$steam['w_icon']}}'" src="{{\App\Models\LgMatch\Team::getLgIcon($steam['icon'])}}">{{$steam['name']}}
                                        </a></td><td>{{$d['value']}}</td></tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            @endif
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