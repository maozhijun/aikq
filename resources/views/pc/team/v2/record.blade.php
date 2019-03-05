@extends('pc.team.v2.base')
@section('detail')
    <div id="Tab_con">
        <p><a href="/{{$name_en}}/team{{$tid}}_index_1.html">综合</a></p>
        <p><a href="/{{$name_en}}/team{{$tid}}_news_1.html">资讯</a></p>
{{--        <p><a href="/{{$name_en}}/team{{$tid}}_videos.html">视频</a></p>--}}
        <p class="on"><a href="#">录像</a></p>
    </div>
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

@section('right')
    <?php
    $sTitle = "";
    ?>
    <div class="con_box">
        <div class="header_con">
            <h4>{{strlen($sTitle) == 0 ? '最新' : $sTitle}}资讯</h4>
            {{--<a href="{{isset($zhuanti) == 0 ? '/news/':'/'.$zhuanti['name_en'].'/news/'}}">全部{{$sTitle}}资讯</a>--}}
            <a href="/news/">全部资讯</a>
        </div>
        <div class="news">
            @foreach($articles as $index=>$article)
                @if($index < 2)
                    <a href="{{$article['link']}}" class="img_news">
                        <p class="img_box"><img src="{{$article['cover']}}"></p>
                        <h3>{{$article['title']}}</h3>
                    </a>
                @else
                    <a href="{{$article['link']}}" class="text_new"><h4>{{$article['title']}}</h4></a>
                @endif
            @endforeach

        </div>
    </div>
@endsection