<?php
$firstName = "goal";
$names = [
    'goal'=>'进球', 'assist'=>'助攻', 'shots'=>'射门', 'target'=>'射正', 'best'=>'最佳',
    'rating'=>'总评', 'playingTime'=>'时间', 'red'=>'红牌', 'yellow'=>'黄牌', 'offside'=>'越位',
    'cross'=>'过人', 'pass'=>'传球', 'touch'=>'触球', 'tackle'=>'铲断', 'interception'=>'拦截',
    'shotBlock'=>'封堵', 'clearance'=>'解围', 'turnover'=>'失误',
];
$teams = isset($players['teams'])?$players['teams']:array();
?>

<div class="player_con player" style="display: none;">
    <div class="col_con">
        @foreach($names as $name=>$name_zh)
            <p @if($name == $firstName)class="on" @endif forItem="{{$name}}">{{$name_zh}}</p>
        @endforeach
    </div>
    @foreach($players as $name=>$itemPlayers)
        @if(!array_key_exists($name, $names)) @continue @endif
        <div class="player_change_con {{$name}}" style="display: {{$name == $firstName?'':'none'}};">
            <div class="inner_title">
                <p class="name_con">球员</p>
                <p class="team_con">球队</p>
                <p>{{$names[$name]}}</p>
                @if($name == 'goal')
                    <p>点球</p>
                @endif
            </div>
            <div class="inner_con">
                @foreach($itemPlayers as $index=>$item)
                    @if(isset($item['value']) && !empty($item['value']))
                        <?php $team = isset($teams[$item['tid']]) ? $teams[$item['tid']] : array();?>
                        <div class="inner_item">
                            <p class="number_con">{{$index+1}}</p>
                            <p class="name_con">{{$item['pname']}}</p>
                            <p class="team_con">{{isset($team['name'])?$team['name']:""}}</p>
                            <p>{{$item['value']}}</p>
                            @if($name == 'goal')
                                <p>{{$item['penalty']}}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</div>