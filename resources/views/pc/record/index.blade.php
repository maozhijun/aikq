@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/record_list_2.css?time=20192191536">
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$zhuanti['name']}}数据
            </div>
        </div>
    @endif
    <div id="Date">
        <div class="def_content">
            <a href="/record/" class="date_con on">全部</a>
            <?php if (!isset($subjects)) $subjects = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();?>
            @if(isset($subjects) && count($subjects) > 0)
                @foreach($subjects as $id=>$su_obj)
                    <a href="/record/{{$su_obj['name_en']}}/" class="date_con">{{$su_obj['name']}}</a>
                @endforeach
            @endif
            <div class="date"><input type="text" name="date" placeholder="选择日期"></div>
        </div>
    </div>
    <div class="def_content" id="Content">
        @foreach($datas as $key=>$data)
            <?php
            $m = explode('-',$key)[1];
            $d = explode('-',$key)[2];
            ?>
            <div id="{{$m}}_{{$d}}" class="el_con">
                <div class="header">
                    <h3><p>{{$m}}月{{$d}}日</p></h3>
                </div>
                <table>
                    <col width="8.2%"><col width="16.6%"><col width="9.8%"><col><col width="15%"><col><col width="20%">
                    @foreach($data['records'] as $record)
                        <?php
                        $type = $record['sport'] == 1 ? 'foot' : 'basket';
                        $timeStr = date('H:i',date_create($record['time'])->getTimestamp());
                        $subject = isset($subjects[$record['s_lid']])? $subjects[$record['s_lid']]['name_en'] : 'other';
                        ?>
                        <tr type="{{$type}}ball">
                            <td><img class="icon" src="{{env('CDN_URL')}}/img/pc/v2/icon_{{$type}}_light_opaque.png"></td>
                            <td>{{$record['lname']}}</td>
                            <td>{{$timeStr}}</td>
                            <td>{{$record['hname']}}</td>
                            <td>{{$record['hscore']}} - {{$record['ascore']}}</td>
                            <td>{{$record['aname']}}</td>
                            <td class="channel"><a target="_blank" href="/{{$subject}}/record{{$record['mid']}}.html">观看录像</a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/jquery-ui.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/record_list_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection