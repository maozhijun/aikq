@if(isset($ranks) && count($ranks) > 0)
    <?php
        if (!isset($subject['type'])) {
            $subject['type'] = 1;
        }
    ?>

    @if($subject['sport'] == 1)
        @if($subject['type'] == 1)
            <p class="title">{{$subject["name"]}}积分榜</p>
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
                    @if(isset($rank['tid']))
                        <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($rank['sport'], $rank['lid'], $rank['tid'])}}">{{$rank['name']}}</a></td>
                    @else
                        <td>{{$rank['name']}}</td>
                    @endif
                    <td>{{$rank['win']}}/{{$rank['draw']}}/{{$rank['lose']}}</td>
                    <td>{{$rank['score']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
            <p class="title">{{$subject["name"]}}小组积分榜</p>
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
                        @if(isset($score['tid']))
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($score['sport'], $score['lid'], $score['tid'])}}">{{$score['name']}}</a></td>
                        @else
                            <td>{{$score['name']}}</td>
                        @endif
                    <td>{{$score['win']}}/{{$score['draw']}}/{{$score['lose']}}</td>
                    <td>{{$score['score']}}</td>
                </tr>
                @endforeach
                </tbody>
                @endforeach
            </table>
        @endif
    @elseif ($subject['sport'] == 2 && $subject['lid'] == 1)
        <p class="title">积分榜<button value="East">东部</button><button class="on" value="West">西部</button></p>
        <table id="East" style="display: none;">
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
                    @if(isset($east['tid']))
                        <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($east['sport'], $east['lid'], $east['tid'])}}">{{$east['name']}}</a></td>
                    @else
                        <td>{{$east['name']}}</td>
                    @endif
                    <td>{{$east['win']}}/{{$east['lose']}}</td>
                    <td>{{$east['win_p']}}%</td>
                </tr>
            @endforeach
            @endif
            </tbody>
        </table>
        <table id="West">
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
                        @if(isset($west['tid']))
                            <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($west['sport'], $west['lid'], $west['tid'])}}">{{$west['name']}}</a></td>
                        @else
                            <td>{{$west['name']}}</td>
                        @endif
                        <td>{{$west['win']}}/{{$west['lose']}}</td>
                        <td>{{$west['win_p']}}%</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    @elseif($subject['sport'] == 2)
        <p class="title">{{$subject["name"]}}排名</p>
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
                    @if(isset($rank['tid']))
                        <td><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($rank['sport'], $rank['lid'], $rank['tid'])}}">{{$rank['name']}}</a></td>
                    @else
                        <td>{{$rank['name']}}</td>
                    @endif
                    <td>{{$rank['win']}}/{{$rank['lose']}}</td>
                    <td>{{$rank['win_p']}}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endif