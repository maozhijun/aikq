<?php $display = isset($display) ? $display : "none"; ?>
<div class="rank_con rank" style="display: {{$display}};">
    @foreach($ranks as $name=>$itemRanks)
        @if(strlen($name) > 0 && !is_numeric($name))
            <p class="title_text">{{$name}}组</p>
        @endif
        <table>
            <col width="8%"><col><col width="8%"><col width="8%"><col width="8%"><col width="10.5%"><col width="10.5%"><col width="12%">
            <tr>
                <th></th>
                <th>球队</th>
                <th>胜</th>
                <th>平</th>
                <th>负</th>
                <th>进球</th>
                <th>失球</th>
                <th>积分</th>
            </tr>
            @foreach($itemRanks as $item)
                <tr>
                    <td>{{$item['rank']}}</td>
                    <td><img src="{{$item['ticon']}}">{{empty($item['tname_short'])?$item['tname']:$item['tname_short']}}</td>
                    <td>{{$item['win']}}</td>
                    <td>{{$item['draw']}}</td>
                    <td>{{$item['lose']}}</td>
                    <td>{{$item['goal']}}</td>
                    <td>{{$item['fumble']}}</td>
                    <td>{{$item['score']}}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
</div>