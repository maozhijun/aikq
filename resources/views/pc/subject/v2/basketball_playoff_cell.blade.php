@if(isset($playoff))
    @if(isset($playoff['west']) && isset($playoff['east']))
        <div class="knockout_con basketball">
            <?php
                $eastPlayoff = array();
                foreach ($playoff['east'] as $items) {
                    $count = count($items);
                    if ($count == 4) { //第一圈
                        $eastPlayoff['first'] = $items;
                    } else if ($count == 2) { //半决赛
                        $eastPlayoff['half'] = $items;
                    } else if ($count == 1) { //决赛
                        $eastPlayoff['final'] = collect($items)->values()->first();
                    }
                }
                $westPlayoff = array();
                foreach ($playoff['west'] as $items) {
                    $count = count($items);
                    if ($count == 4) { //第一圈
                        $westPlayoff['first'] = $items;
                    } else if ($count == 2) { //半决赛
                        $westPlayoff['half'] = $items;
                    } else if ($count == 1) { //决赛
                        $westPlayoff['final'] = collect($items)->values()->first();
                    }
                }
            ?>
            <div class="round_con">
                <?php
                    $_1_8 = array(); $_4_5 = array(); $_2_7 = array(); $_3_6 = array();
                    if (isset($eastPlayoff['first'])) {
                        foreach($eastPlayoff['first'] as $key=>$item) {
                            if (starts_with($key, "1_")) {
                                $_1_8 = $item;
                            } else if (starts_with($key, "2_")) {
                                $_2_7 = $item;
                            } else if (starts_with($key, "3_")) {
                                $_3_6 = $item;
                            } else if (starts_with($key, "4_")) {
                                $_4_5 = $item;
                            }
                        }
                    }
                ?>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_1_8, 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_4_5, 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 30px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_2_7, 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_3_6, 'lid'=>$lid])
                </div>
                <div class="line_left_con" style="height: 82px; top: 32px; left: 10px;"></div>
                <div class="line_left_con" style="height: 82px; top: 210px; left: 10px;"></div>
            </div>
            <div class="round_con">
                <?php
                    $half_up = array(); $half_down = array();
                    if (isset($eastPlayoff['half'])) {
                        foreach($eastPlayoff['half'] as $key=>$item) {
                            $rank = explode("_", $key)[0];
                            $upRanks = ['1','4','5','8'];
                            $downRanks = ['2','3','6','7'];

                            if (in_array($rank, $upRanks)) { //上半区
                                $half_up = $item;
                            } else if (in_array($rank, $downRanks)) { //下半区
                                $half_down = $item;
                            }
                        }
                    }
                ?>
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$half_up, 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$half_down, 'lid'=>$lid])
                </div>
                <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($eastPlayoff['final']) ? $eastPlayoff['final'] : array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['final']) ? $westPlayoff['final'] : array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <?php
                    $half_up = array(); $half_down = array();
                    if (isset($westPlayoff['half'])) {
                        foreach($westPlayoff['half'] as $key=>$item) {
                            $rank = explode("_", $key)[0];
                            $upRanks = ['1','4','5','8'];
                            $downRanks = ['2','3','6','7'];

                            if (in_array($rank, $upRanks)) { //上半区
                                $half_up = $item;
                            } else if (in_array($rank, $downRanks)) { //下半区
                                $half_down = $item;
                            }
                        }
                    }
                ?>
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$half_up, 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$half_down, 'lid'=>$lid])
                </div>
                <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
            </div>
            <div class="round_con">
                <?php
                    $_1_8 = array(); $_4_5 = array(); $_2_7 = array(); $_3_6 = array();
                    if (isset($westPlayoff['first'])) {
                        foreach($westPlayoff['first'] as $key=>$item) {
                            if (starts_with($key, "1_")) {
                                $_1_8 = $item;
                            } else if (starts_with($key, "2_")) {
                                $_2_7 = $item;
                            } else if (starts_with($key, "3_")) {
                                $_3_6 = $item;
                            } else if (starts_with($key, "4_")) {
                                $_4_5 = $item;
                            }
                        }
                    }
                ?>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_1_8, 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_4_5, 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 30px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_2_7, 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$_3_6, 'lid'=>$lid])
                </div>
                <div class="line_right_con" style="height: 82px; top: 32px; right: 10px;"></div>
                <div class="line_right_con" style="height: 82px; top: 210px; right: 10px;"></div>
            </div>
            <?php $item = isset($playoff['final']) ? collect($playoff['final'])->values()->first() : null; ?>
            @include('pc.subject.v2.basketball_playoff_final_cell', ['item'=>$item])
        </div>
    @elseif(isset($playoff['west']))
        <div class="knockout_con basketball">
            <?php
                $addPlayoff = array(); //十进八
                if (isset($playoff['add'])) {
                    $tempData = collect($playoff['add'])->values()->first();
                    foreach ($tempData as $key=>$item) {
                        $rank = explode("_", $key)[1];
                        $upRanks = ['8','9'];
                        $downRanks = ['7','10'];
                        if (in_array($rank, $upRanks)) { //上半区
                            $addPlayoff['up'] = $item;
                        } else if (in_array($rank, $downRanks)) { //下半区
                            $addPlayoff['down'] = $item;
                        }
                    }
                }
                $westPlayoff = array();
                foreach ($playoff['west'] as $items) {
                    $count = count($items);
                    if ($count == 4) { //第一圈
                        foreach ($items as $key=>$item) {
                            $rank = explode("_", $key)[0];
                            $upRanks = ['1','4','5'];
                            $downRanks = ['2','3','6'];
                            if (in_array($rank, $upRanks)) { //上半区
                                if ($rank == "1") {
                                    $westPlayoff['1_8'] = $item;
                                } else if ($rank == "4") {
                                    $westPlayoff['4_5'] = $item;
                                }
                            } else if (in_array($rank, $downRanks)) { //下半区
                                if ($rank == "2") {
                                    $westPlayoff['2_7'] = $item;
                                } else if ($rank == "3") {
                                    $westPlayoff['3_6'] = $item;
                                }
                            }
                        }
                    } else if ($count == 2) { //半决赛
                        foreach ($items as $key=>$item) {
                            $rank = explode("_", $key)[0];
                            $upRanks = ['1','4','5'];
                            $downRanks = ['2','3','6'];
                            if (in_array($rank, $upRanks)) { //上半区
                                $westPlayoff['half_up'] = $item;
                            } else if (in_array($rank, $downRanks)) { //下半区
                                $westPlayoff['half_down'] = $item;
                            }
                        }
                    } else if ($count == 1) { //决赛
                        $westPlayoff['final'] = collect($items)->values()->first();
                    }
                }
            ?>
            @if(isset($addPlayoff['up']))
                <div class="round_con">
                    <div class="match_con">
                        @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$addPlayoff['up'], 'lid'=>$lid])
                    </div>
                    <div class="line_left_con" style="height: 41px; top: 32px; left: 10px; border-bottom: none;"></div>
                </div>
            @endif
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['1_8']) ? $westPlayoff['1_8'] : array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['4_5']) ? $westPlayoff['4_5'] : array(), 'lid'=>$lid])
                </div>
                <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['half_up']) ? $westPlayoff['half_up'] : array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['half_down']) ? $westPlayoff['half_down'] : array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['2_7']) ? $westPlayoff['2_7'] : array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($westPlayoff['3_6']) ? $westPlayoff['3_6'] : array(), 'lid'=>$lid])
                </div>
                <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
            </div>
            @if(isset($addPlayoff['down']))
                <div class="round_con">
                    <div class="match_con">
                        @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$addPlayoff['down'], 'lid'=>$lid])
                    </div>
                    <div class="line_right_con" style="height: 41px; top: 32px; right: 10px; border-bottom: none;"></div>
                </div>
            @endif
            <?php $item = isset($westPlayoff['final']) ? $westPlayoff['final'] : null; ?>
            @include('pc.subject.v2.basketball_playoff_final_cell', ['item'=>$item])
        </div>
    @endif
@endif