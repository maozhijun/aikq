@if(isset($playoff))
    @if(isset($playoff['west']) && isset($playoff['east']))
        <div class="knockout_con basketball">
            <div class="round_con">
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['up']['first'][0])?$playoff['east']['up']['first'][0]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['up']['first'][1])?$playoff['east']['up']['first'][1]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 30px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['down']['first'][0])?$playoff['east']['down']['first'][0]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['down']['first'][1])?$playoff['east']['down']['first'][1]:array(), 'lid'=>$lid])
                </div>
                <div class="line_left_con" style="height: 82px; top: 32px; left: 10px;"></div>
                <div class="line_left_con" style="height: 82px; top: 210px; left: 10px;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['up']['half'])?$playoff['east']['up']['half']:array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['down']['half'])?$playoff['east']['down']['half']:array(), 'lid'=>$lid])
                </div>
                <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['east']['final'])?$playoff['east']['final']:array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['final'])?$playoff['west']['final']:array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['half'])?$playoff['west']['up']['half']:array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['half'])?$playoff['west']['down']['half']:array(), 'lid'=>$lid])
                </div>
                <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][0])?$playoff['west']['up']['first'][0]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][1])?$playoff['west']['up']['first'][1]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 30px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][0])?$playoff['west']['down']['first'][0]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][1])?$playoff['west']['down']['first'][1]:array(), 'lid'=>$lid])
                </div>
                <div class="line_right_con" style="height: 82px; top: 32px; right: 10px;"></div>
                <div class="line_right_con" style="height: 82px; top: 210px; right: 10px;"></div>
            </div>
            @include('pc.subject.v2.basketball_playoff_final_cell', ['item'=>isset($playoff['final']) ? $playoff['final'] : null])
        </div>
    @elseif(isset($playoff['west']))
        <div class="knockout_con basketball">
            @if(isset($playoff['add']['up']))
                <div class="round_con">
                    <div class="match_con">
                        @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$playoff['add']['up'], 'lid'=>$lid])
                    </div>
                    <div class="line_left_con" style="height: 41px; top: 32px; left: 10px; border-bottom: none;"></div>
                </div>
            @endif
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][0])?$playoff['west']['up']['first'][0]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][1])?$playoff['west']['up']['first'][1]:array(), 'lid'=>$lid])
                </div>
                <div class="line_left_con" style="height: 176px; top: 74px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['half'])?$playoff['west']['up']['half']:array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 188px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['half'])?$playoff['west']['down']['half']:array(), 'lid'=>$lid])
                </div>
                <div class="line_con" style="height: 0; top: 219px; left: 0;"></div>
            </div>
            <div class="round_con">
                <div class="match_con" style="margin-top: 42px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][0])?$playoff['west']['down']['first'][0]:array(), 'lid'=>$lid])
                </div>
                <div class="match_con" style="margin-top: 114px;">
                    @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][1])?$playoff['west']['down']['first'][1]:array(), 'lid'=>$lid])
                </div>
                <div class="line_right_con" style="height: 176px; top: 74px; right: 0;"></div>
            </div>
            @if(isset($playoff['add']['down']))
                <div class="round_con">
                    <div class="match_con">
                        @include('pc.subject.v2.basketball_playoff_match_con_cell', ['item'=>$playoff['add']['down'], 'lid'=>$lid])
                    </div>
                    <div class="line_right_con" style="height: 41px; top: 32px; right: 10px; border-bottom: none;"></div>
                </div>
            @endif
            @include('pc.subject.v2.basketball_playoff_final_cell', ['item'=>isset($playoff['west']['final']) ? $playoff['west']['final'] : null])
        </div>
    @endif
@endif