@extends('pc.team.v2.base')
@section('detail')
    <table class="match">
        <col width="12%"><col width="19%"><col><col width="15%"><col><col width="16%">
        @foreach($records as $record)
            <?php
            $type = $record['sport'] == 1 ? 'foot' : 'basket';
            $timeStr = date('Y:m:d H:i',date_create($record['time'])->getTimestamp());
            $subject = isset($subjects[$record['s_lid']])? $subjects[$record['s_lid']]['name_en'] : 'other';
            if (!is_null($record->url)){
                $url = $record->url;
            }
            else{
                $url = \App\Http\Controllers\PC\CommonTool::getRecordDetailUrl($subject,$record['mid']);
            }
            ?>
            <tr>
                <td><span>{{$record['lname']}}</span></td>
                <td><span>{{$timeStr}}</span></td>
                <td class="host"><a href="">{{$record['hname']}}</a></td>
                <td class="vs">{{$record['hscore']}} - {{$record['ascore']}}</td>
                <td class="away"><a href="">{{$record['aname']}}</a></td>
                <td><a class="record" target="_blank" href="{{$url}}">观看录像</a></td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6">
                @if($page > 1)
                    @component("pc.layout.v2.page_cell", ['lastPage'=>$page, "curPage"=>$pageNo,'href'=>'/'.$name_en.'/team'.$tid.'_record_']) @endcomponent
                @endif
            </td>
        </tr>
    </table>
@endsection