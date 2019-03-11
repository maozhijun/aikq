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
                        $eastPlayoff['final'] = $items;
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
                        $westPlayoff['final'] = $items;
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
                <?php
                    if (isset($eastPlayoff['final'])) {
                        $final = collect($eastPlayoff['final'])->values()->first();
                    } else {
                        $final = array();
                    }
                ?>
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$final, 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <?php
                    if (isset($westPlayoff['final'])) {
                        $final = collect($westPlayoff['final'])->values()->first();
                    } else {
                        $final = array();
                    }
                ?>
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$final, 'lid'=>$lid])
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
            @if(isset($playoff['final']))
                @foreach($playoff['final'] as $item)
                    <div class="finals_match">
                    <img src="/img/pc/v2/image_basketball_n.png" class="cup">
                    @if(isset($item['info']))
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
                        <div class="team_con">
                            <p class="team"><img src="{{$hicon}}"><span>{{$hname}}</span></p>
                            <p class="score">{{$hscore}}&nbsp;&nbsp;&nbsp;{{$ascore}}</p>
                            <p class="team"><img src="{{$aicon}}"><span>{{$aname}}</span></p>
                        </div>
                    @endif
                    <ul>
                        @foreach($item['matches'] as $match)
                            <li>
                                <a href="live.html">
                                    @if($match['hscore'] > $match['ascore'])
                                        <p class="icon"><img src="/img/pc/v2/image_basketball_n.png"></p>
                                    @else
                                        <p class="icon"></p>
                                    @endif
                                    <p class="host">{{$match['hid'] == $hid ? $hname : $aname}}</p>
                                    <p class="score">{{$match['hscore']}}</p>
                                    <p class="vs">-</p>
                                    <p class="score">{{$match['ascore']}}</p>
                                    <p class="away">{{$match['aid'] == $aid ? $aname : $hname}}</p>
                                    @if($match['hscore'] < $match['ascore'])
                                        <p class="icon"><img src="/img/pc/v2/image_basketball_n.png"></p>
                                    @else
                                        <p class="icon"></p>
                                    @endif
                                </a>
                            </li>
                            {{--<li><p>-</p></li>--}}
                        @endforeach
                    </ul>
                </div>
                @endforeach
            @endif
        </div>
    @elseif(isset($playoff['west']))
        <div class="knockout_con basketball">
            <?php
                $addPlayoff = array(); //十进八
                if (isset($playoff['add'])) {
                    $addPlayoff = collect($playoff['add'])->values()->first();
                }
                $westPlayoff = array();
                foreach ($playoff['west'] as $items) {
                    $count = count($items);
                    if ($count == 4) { //第一圈
                        $westPlayoff['first'] = $items;
                    } else if ($count == 2) { //半决赛
                        $westPlayoff['half'] = $items;
                    } else if ($count == 1) { //决赛
                        $westPlayoff['final'] = $items;
                    }
                }
            ?>
            <div class="round_con">
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html"><a href="team.html">凯尔特人</a></a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="line_left_con" style="height: 41px; top: 32px; left: 10px; border-bottom: none;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">开拓者</a></p>
                </div>
                <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con">
                    <p><b class="win">4</b><a href="team.html">凯尔特人</a></p>
                    <p><b>2</b><a href="team.html">勇士</a></p>
                </div>
                <div class="line_right_con" style="height: 41px; top: 32px; right: 10px; border-bottom: none;"></div>
            </div>
            <div class="finals_match">
                <img src="img/image_basketball_n.png" class="cup">
                <div class="team_con">
                    <p class="team"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/15.png"><span>湖人</span></p>
                    <p class="score">3&nbsp;&nbsp;&nbsp;2</p>
                    <p class="team"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/15.png"><span>凯尔特人</span></p>
                </div>
                <ul>
                    <li>
                        <a href="live.html">
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                            <p class="host">湖人</p>
                            <p class="score">104</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                        </a>
                    </li>
                    <li>
                        <a href="live.html">
                            <p class="icon"><img src="img/image_basketball_n.png"></p>
                            <p class="host">湖人</p>
                            <p class="score">98</p>
                            <p class="vs">-</p>
                            <p class="score">102</p>
                            <p class="away">凯尔特人</p>
                            <p class="icon"></p>
                        </a>
                    </li>
                    <li><p>-</p></li>
                    <li><p>-</p></li>
                </ul>
            </div>
        </div>
    @endif
@endif