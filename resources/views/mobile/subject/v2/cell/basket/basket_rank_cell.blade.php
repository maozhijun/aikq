<div class="rank_con rank" style="display: none;">
    @foreach($ranks as $name=>$itemRanks)
        @if(strlen($name) > 0 && !is_numeric($name))
            <p class="title_text">{{$name}}</p>
        @endif
        <table>
            <col width="8%"><col><col width="16%"><col width="16%"><col width="21%">
            <tr>
                <th></th>
                <th>球队</th>
                <th>胜</th>
                <th>负</th>
                <th>胜场差</th>
            </tr>
            @foreach($itemRanks as $item)
                <tr>
                    <td>{{$item['rank']}}</td>
                    <td><img src="{{$item['ticon']}}">{{empty($item['tname_short'])?$item['tname']:$item['tname_short']}}</td>
                    <td>{{$item['win']}}</td>
                    <td>{{$item['lose']}}</td>
                    <td>{{$item['win_diff']}}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
</div>