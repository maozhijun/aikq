@if(isset($ranks) && count($ranks) > 0)
    <?php
        $divCss = ($subject['type'] == 2) ? 'cup' : 'league';
    ?>
    <div class="default {{$divCss}}" id="Rank"><!--足球联赛-->
        @if($subject['sport'] == 1)
            @if($subject['type'] == 1)
            <div class="title">
                <p>{{$subject["name"]}}积分榜</p>
            </div>
            <table>
            <colgroup>
                <col width="34px">
                <col width="">
                <col width="54px">
                <col width="48px">
            </colgroup>
            <thead>
            <tr>
                <th>排名</th>
                <th>球队</th>
                <th>胜/平/负</th>
                <th>积分</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ranks as $rank)
                <tr>
                    <td><span>{{$rank['rank']}}</span></td>
                    <td>{{$rank['name']}}</td>
                    <td>{{$rank['win']}}/{{$rank['draw']}}/{{$rank['lose']}}</td>
                    <td>{{$rank['score']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
            @else
                <div class="title">
                    <p>{{$subject["name"]}}小组积分榜</p>
                </div>
                <table>
                    <colgroup>
                        <col width="34px">
                        <col width="">
                        <col width="54px">
                        <col width="48px">
                    </colgroup>
                    @foreach($ranks as $g=>$scores)
                    <thead>
                    <tr>
                        <th>{{$g}}组</th>
                        <th>球队</th>
                        <th>胜/平/负</th>
                        <th>积分</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($scores as $index=>$score)
                        <tr>
                        <td><span>{{$index + 1}}</span></td>
                        <td>{{$score['name']}}</td>
                        <td>{{$score['win']}}/{{$score['draw']}}/{{$score['lose']}}</td>
                        <td>{{$score['score']}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    @endforeach
                </table>
            @endif
        @elseif ($subject['sport'] == 2 && $subject['lid'] == 1)
            <div class="tab">
                <button class="on" value="East">东部排名</button>
                <button value="West">西部排名</button>
            </div>
            <table id="East">
                <colgroup>
                    <col width="34px">
                    <col width="">
                    <col width="54px">
                    <col width="48px">
                </colgroup>
                <thead>
                <tr>
                    <th>排名</th>
                    <th>球队</th>
                    <th>胜/负</th>
                    <th>胜率</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($ranks['east']))
                @foreach($ranks['east'] as $east)
                    <tr>
                        <td><span>{{$east['rank']}}</span></td>
                        <td>{{$east['name']}}</td>
                        <td>{{$east['win']}}/{{$east['lose']}}</td>
                        <td>{{$east['win_p']}}%</td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
            <table id="West" style="display: none;">
                <colgroup>
                    <col width="34px">
                    <col width="">
                    <col width="54px">
                    <col width="48px">
                </colgroup>
                <thead>
                <tr>
                    <th>排名</th>
                    <th>球队</th>
                    <th>胜/负</th>
                    <th>胜率</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($ranks['west']))
                    @foreach($ranks['west'] as $west)
                        <tr>
                            <td><span>{{$west['rank']}}</span></td>
                            <td>{{$west['name']}}</td>
                            <td>{{$west['win']}}/{{$west['lose']}}</td>
                            <td>{{$west['win_p']}}%</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        @elseif($subject['sport'] == 2)
            <div class="title">
                <p>{{$subject["name"]}}排名</p>
            </div>
            <table>
                <colgroup>
                    <col width="34px">
                    <col width="">
                    <col width="54px">
                    <col width="48px">
                </colgroup>
                <thead>
                <tr>
                    <th>排名</th>
                    <th>球队</th>
                    <th>胜/负</th>
                    <th>胜率</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ranks as $rank)
                    <tr>
                        <td><span>{{$rank['rank']}}</span></td>
                        <td>{{$rank['name']}}</td>
                        <td>{{$rank['win']}}/{{$rank['lose']}}</td>
                        <td>{{$rank['win_p']}}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endif