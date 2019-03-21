<?php
    $firstName = "ppg";
    $names = [
        'ppg'=>'得分', 'fp_rate'=>'投篮%', 'three_p_rate'=>'三分%', 'ft_rate'=>'罚球%',
        'rpg'=>'篮板', 'apg'=>'助攻', 'bpg'=>'盖帽', 'spg'=>'抢断',
        'mpg'=>'失误', 'fpg'=>'犯规', 'double2'=>'两双', 'double3'=>'三双',
    ];
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
            <p><p>{{$names[$name]}}</p></p>
            <p>场次</p>
        </div>
        <div class="inner_con">
            @foreach($itemPlayers as $index=>$item)
                @if(isset($item[$name]) && !empty($item[$name]))
                    <div class="inner_item">
                        <p class="number_con">{{$index+1}}</p>
                        <p class="name_con">{{$item['name']}}</p>
                        <p class="team_con">{{$item['tname']}}</p>
                        <p>{{$item[$name]}}</p>
                        <p>{{$item['count']}}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endforeach
</div>