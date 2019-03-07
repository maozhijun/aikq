<?php
    $index = 0;$style = "";
    $continue = count($knockouts[$count]) / 2;
?>
@foreach($knockouts[$count] as $kk)
    <?php $index++; ?>
    @continue($p == "before" ? $index > $continue : $index < $continue + 1)
    <?php
        if ($count == 8) {
            if (empty($style)) {
                $style = 'style="margin-top: 42px;"';
            } else {
                $style = 'style="margin-top: 114px;"';
            }
        } elseif ($count == 4) {
            $style = 'style="margin-top: 188px;"';
        }
    ?>
    @if(empty($kk["host"]["id"]))
        <div class="match_con" {!! $style !!} >
            <p><b></b><a></a></p>
            <p><b></b><a></a></p>
        </div>
    @else
    <?php
        $hTUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sl["sport"], $sl["lid"], $kk["host"]["id"]);
        $aTUrl = \App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sl["sport"], $sl["lid"], $kk["away"]["id"]);
        $hScore = $kk["host"]["score"];
        $aScore = $kk["away"]["score"];
        $isHostWin = $hScore > $aScore;
        $isAwayWin = $aScore > $hScore;
    ?>
    <div class="match_con" {!! $style !!} >
        <p><b @if($isHostWin) class="win" @endif >{{$hScore}}</b><a target="_blank" href="{{$hTUrl}}">{{$kk["host"]["name"]}}</a></p>
        <p><b @if($isAwayWin) class="win" @endif >{{$aScore}}</b><a target="_blank" href="{{$aTUrl}}">{{$kk["away"]["name"]}}</a></p>
    </div>
    @endif
@endforeach