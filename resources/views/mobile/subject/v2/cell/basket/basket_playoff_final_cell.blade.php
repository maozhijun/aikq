<div class="finals_con">
    @if(isset($item) && isset($item['info']))
        <?php
        //东部在左，西部在右，需要调节一下顺序
        $hicon = isset($item['info']['hicon']) ? $item['info']['hicon'] : "";
        $aicon = isset($item['info']['aicon']) ? $item['info']['aicon'] : "";
        $hid = $item['info']['hid'];
        $aid = $item['info']['aid'];
        $hname = $item['info']['hname_short'];
        $aname = $item['info']['aname_short'];
        $hscore = $item['info']['hscore'];
        $ascore = $item['info']['ascore'];
        if ($item['info']['hzone'] == 0) {
            $tempIcon = $hicon; $hicon = $aicon; $aicon = $tempIcon;
            $tempName = $hname; $hname = $aname; $aname = $tempName;
            $tempScore = $hscore; $hscore = $ascore; $ascore = $tempScore;
            $tempId = $hid; $hid = $aid; $aid = $tempId;
        }
        ?>
        <img src="{{$hicon}}">
        <p class="team_name">{{$hname}}</p>
        <p class="score_con">{{$hscore}} - {{$ascore}}</p>
        <p class="team_name">{{$aname}}</p>
        <img src="{{$aicon}}">
    @endif
</div>