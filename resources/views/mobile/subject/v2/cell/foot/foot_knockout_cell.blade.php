<?php
    //32强、16强、8强、4强、决赛
    $_32 = array();$_16 = array();$_8 = array();$_4 = array();$final = array();
    //需要把下一轮的球队id传进去
    $finalIds = array(); $_4Ids = array(); $_8Ids = array();; $_16Ids = array();
    if(isset($knockout)) {
        foreach (array_reverse($knockout) as $stage) {
            switch ($stage['name']) {
                case '决赛':
                    if(isset($stage['combo'])) {
                        $tempFinal = $stage['combo'];
                    } else if (isset($stage['matches'])) {
                        $tempFinal = $stage['matches'];
                    }
                    if (isset($tempFinal) && count($tempFinal) > 0) {
                        foreach($tempFinal as $key=>$item) {
                            $finalIds[] = $item['hid'];
                            $finalIds[] = $item['aid'];
                            if (isset($item['matches'][0])) {
                                $item['hicon'] = $item['matches'][0]['hicon'];
                                $item['aicon'] = $item['matches'][0]['aicon'];
                                $item['status'] = $item['matches'][0]['status'];
                            }
                            $final = $item;
                        }
                    }
                    break;
                case '准决赛':
                    if(isset($stage['combo'])) {
                        $_4 = $stage['combo'];
                    } else if (isset($stage['matches'])) {
                        $_4 = $stage['matches'];
                    }
                    if (count($_4) > 0) {
                        foreach($_4 as $key=>$item) {
                            $ids = [$item['hid'], $item['aid']];
                            if (count($finalIds) > 0) {
                                foreach ($finalIds as $a=>$id) {
                                    if (in_array($id, $ids)) {
                                        $_4Ids[$a] = $ids;
                                    }
                                }
                            } else {
                                $_4Ids[] = $ids;
                            }
                        }
                    }
                    break;
                case '半准决赛':
                case '8强':
                    if(isset($stage['combo'])) {
                        $_8 = $stage['combo'];
                    } else if (isset($stage['matches'])) {
                        $_8 = $stage['matches'];
                    }
                    if(count($_8) > 0) {
                        foreach($_8 as $key=>$item) {
                            $ids = [$item['hid'], $item['aid']];
                            if (count($_4Ids) > 0) {
                                foreach ($_4Ids as $a=>$aIds) {
                                    foreach ($aIds as $b=>$id) {
                                        if (in_array($id, $ids)) {
                                            $_8Ids[$a][$b] = $ids;
                                        }
                                    }
                                }
                            } else {
                                if (!isset($_8Ids[0][0])) {
                                    $_8Ids[0][0] = $ids;
                                } else if (!isset($_8Ids[0][1])) {
                                    $_8Ids[0][1] = $ids;
                                } else if (!isset($_8Ids[1][0])) {
                                    $_8Ids[1][0] = $ids;
                                } else if (!isset($_8Ids[1][1])) {
                                    $_8Ids[1][1] = $ids;
                                }
                            }
                        }
                    }
                    break;
                case '十六强':
                case '16强':
                    if(isset($stage['combo'])) {
                        $_16 = $stage['combo'];
                    } else if (isset($stage['matches'])) {
                        $_16 = $stage['matches'];
                    }
                    break;
                case '三十二强':
                case '32强':
                    if(isset($stage['combo'])) {
                        $_32 = $stage['combo'];
                    }
                    break;
            }
        }
    }
?>
<div class="knockout_con knockout" style="display: ">
    <div class="part_con">
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[0][0][0])?$_8Ids[0][0][0]:null, 'index'=>0])
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[0][0][1])?$_8Ids[0][0][1]:null, 'index'=>1])
        </div>
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_8, 'outId'=>isset($_4Ids[0][0])?$_4Ids[0][0]:null, 'index'=>0])
        </div>
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_8, 'outId'=>isset($_4Ids[0][1])?$_4Ids[0][1]:null, 'index'=>1])
        </div>
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[0][1][0])?$_8Ids[0][1][0]:null, 'index'=>2])
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[0][1][1])?$_8Ids[0][1][1]:null, 'index'=>3])
        </div>
        <div class="part_item semi-final">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_4, 'outId'=>isset($finalIds[0])?$finalIds[0]:null, 'index'=>0])
        </div>
    </div>
    <div class="finals_con">
        @if(isset($final) && count($final) > 0)
            <img src="{{$final['hicon']}}">
            <p class="team_name">{{$final['hname']}}</p>
            @if($final['status'] == -1)
                <p class="score_con">{{$final['hscore']}} - {{$final['ascore']}}</p>
            @else
                <p class="score_con">VS</p>
            @endif
            <p class="team_name">{{$final['aname']}}</p>
            <img src="{{$final['aicon']}}">
        @endif
    </div>
    <div class="part_con">
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[1][0][0])?$_8Ids[1][0][0]:null, 'index'=>4])
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[1][0][1])?$_8Ids[1][0][1]:null, 'index'=>5])
        </div>
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_8, 'outId'=>isset($_4Ids[1][0])?$_4Ids[1][0]:null, 'index'=>2])
        </div>
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_8, 'outId'=>isset($_4Ids[1][1])?$_4Ids[1][1]:null, 'index'=>3])
        </div>
        <div class="part_item">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[1][1][0])?$_8Ids[1][1][0]:null, 'index'=>6])
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_16, 'outId'=>isset($_8Ids[1][1][1])?$_8Ids[1][1][1]:null, 'index'=>7])
        </div>
        <div class="part_item semi-final">
            @include('mobile.subject.v2.cell.foot.foot_knockout_match_con_cell', ['combo'=>$_4, 'outId'=>isset($finalIds[1])?$finalIds[1]:null, 'index'=>1])
        </div>
    </div>
</div>