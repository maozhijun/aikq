<?php
    $i = -1;
    $index = isset($index) ? $index : 0;
?>
<div class="match_con">
    @if(isset($combo) && count($combo) > 0)
        @foreach($combo as $key=>$item)
            <?php
                $i++;
                $hid = $item['hid']; $aid = $item['aid'];
                $isHomeLose = isset($outId) && $aid == $outId;
                $isAwayLose = isset($outId) && $hid == $outId;
                $isInArray = (isset($outId) && in_array($outId, [$hid, $aid])) || (!isset($outId) && $index == $i);
            ?>
            @if($isInArray)
                <p @if($isHomeLose)class="lose" @endif><span>{{isset($item['hscore'])?$item['hscore']:"-"}}</span>{{$item['hname']}}</p>
                <p @if($isAwayLose)class="lose" @endif><span>{{isset($item['ascore'])?$item['ascore']:"-"}}</span>{{$item['aname']}}</p>
            @endif
        @endforeach
    @endif
</div>
