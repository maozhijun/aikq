@extends('mobile.fifa.base')
@section('content')
    <div id="Topbar">
        <a href="{{env('M_URL')}}">直播</a>
        <a href="{{env('M_URL')}}/live/subject/videos/all/1.html">录像</a>
        <a href="https://shop.liaogou168.com/article/recommends">推荐</a>
        <a class="on">世界杯</a>
    </div>
    <div id="roundTab">
        <p class="on" for="group">积分榜</p>
        <p for="goal">射手榜</p>
        {{--<p for="assists">助攻榜</p>--}}
    </div>
    <div id="group">
        <?php
        $groups = $score['stages'][0]['groupMatch'];
        ?>
        @foreach($groups as $key=>$group)
            <div class="matchList" id="{{$key}}">
                <table>
                    <thead>
                    <tr>
                        <th>{{$key}}组</th>
                        <th>球队</th>
                        <th>赛</th>
                        <th>胜/平/负</th>
                        <th>进/失</th>
                        <th>净</th>
                        <th>积分</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $scores = $group['scores'];
                    ?>
                    @for($i = 0 ; $i < count($scores) ; $i++)
                        <?php
                        $score = $scores[$i];
                        ?>
                        <tr id="rank_t_{{$score['tid']}}">
                            <td>{{$i+1}}</td>
                            <td>{{$score['tname']}}</td>
                            <td>{{$score['count']}}</td>
                            <td>{{$score['win']}}/{{$score['draw']}}/{{$score['lose']}}</td>
                            <td>{{$score['goal']}}/{{$score['fumble']}}</td>
                            <td>{{$score['goal'] - $score['fumble']}}</td>
                            <td>{{$score['score']}}</td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>
            @if($loop->index == 7)
            @else
                <div class="separated"></div>
            @endif
        @endforeach
    </div>
    <div id="goal" class="rank" style="display: none;">
        <table>
            <thead>
            <tr>
                <th>排名</th>
                <th>球员</th>
                <th>球队</th>
                <th>进球</th>
            </tr>
            @for($i = 0 ; $i < min(3,count($rank['total'])); $i++)
                <?php
                $item = $rank['total'][$i];
                ?>
                <tr>
                    <td>{{$i + 1}}</td>
                    <td><img src="{{isset($item['icon'])?$item['icon']:''}}" onerror="this.src = '{{env('CDN_URL')}}/img/mobile/fifa/image_player_n.jpg'">{{$item['name']}}</td>
                    <td>{{$item['team']}}</td>
                    <td>{{$item['goal']}}</td>
                </tr>
            @endfor
            </thead>
            <tbody>
            @for($i = 3 ; $i < count($rank['total']); $i++)
                <?php
                $item = $rank['total'][$i];
                ?>
                <tr>
                    <td>{{$i + 1}}</td>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['team']}}</td>
                    <td>{{$item['goal']}}</td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
    @component('mobile.fifa.base_bottom',['index'=>2])
    @endcomponent
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/rank.css">
    <style>
        #group table{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/image_bg_nav_n_copy.jpg) no-repeat center; background-size: cover;!important;
        }
        #group table tr:nth-child(1) td:first-child, #group table tr:nth-child(2) td:first-child{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_number_n.png) no-repeat center;background-size: 50px!important;
        }
        .separated.live:before, .separated.live:after{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_live_n.png) no-repeat center;background-size: 40px !important;
        }
        .separated.noStart:before, .separated.noStart:after{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_future_n.png) no-repeat center;background-size: 40px !important;
        }
        .separated.end:before, .separated.end:after{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_end_n.png) no-repeat center; background-size: 40px!important;
        }
        #group table tr:nth-child(1) td:first-child, #group table tr:nth-child(2) td:first-child{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_number_n.png) no-repeat center; background-size: 50px;
        }
        .rank table thead td:nth-child(1){
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_scorenumber_n.png) no-repeat center #fff; background-size: 50px;
            color: #fff;
        }
    </style>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/rank.js?time=201803030002"></script>
    <script type="text/javascript">
        function refreshScore() {
            var url = {{env('MATCH_URL')}}+"/static/league/1/57.json";
            $.ajax({
                "url": url,
                dataType: "jsonp",
                "success": function (json) {
                    var groups = json['stages'][0]['groupMatch'];
                    for (var key in groups){
                        var group = groups[key]['scores'];
                        for (var i = 0 ; i < group.length ; i++){
                            var score = group[i];
                            var tr = $('div#group tr#rank_t_'+score['tid']);
                            if (tr && tr.length > 0){
                                tr[0].innerHTML = '<td>'+(i+1)+'</td>'+
                                        '<td>'+ score['tname']+'</td>'+
                                        '<td>'+score['count']+'</td>'+
                                        '<td>'+score['win']+'/'+score['draw']+'/'+score['lose']+'</td>'+
                                        '<td>'+score['goal']+'/'+score['fumble']+'</td>'+
                                        '<td>'+(score['goal'] - score['fumble'])+'</td>'+
                                        '<td>'+ score['score'] +'</td>';
                            }
                        }
                    }
//                    setTimeout(refreshScore, 60000);
                },
                "error": function () {
//                    setTimeout(refreshScore, 60000);
                }
            });
        }

        function refreshRank() {
            var url = {{env('MATCH_URL')}}+"/static/league/1/FIFA/2018/rank.json";
            $.ajax({
                "url": url,
                dataType: "jsonp",
                "success": function (json) {
                    var ranks = json['total'];
                    var div = $('div#goal');
                    if (div == null || div.length == 0){
                        return;
                    }
                    var trs = '<tr><th>排名</th><th>球员</th><th>球队</th><th>进球</th></tr>';
                    var trbs = '';
                    div.find('tbody')[0].innerHTML = '';
                    for(var i = 0 ; i < ranks.length ; i++){
                        var item = ranks[i];
                        if (i < 3) {
                            trs = trs + '<tr>' +
                                    '<td>' + (i + 1) + '</td>' +
                                    '<td><img src="' + ((item['icon'] && item['icon'].length > 0) ? item['icon'] : '') + '" onerror="this.src = \'' + '{{env('CDN_URL')}}/img/mobile/fifa/image_player_n.jpg' + '\'">' + item['name'] + '</td>' +
                                    '<td>' + item['team'] + '</td>' +
                                    '<td>' + item['goal'] + '</td>' +
                                    '</tr>'
                        }
                        else{
                            trbs = trbs + '<tr>' +
                                    '<td>' + (i + 1) + '</td>' +
                                    '<td>' + item['name'] + '</td>' +
                                    '<td>' + item['team'] + '</td>' +
                                    '<td>' + item['goal'] + '</td>' +
                                    '</tr>'
                        }
                    }
                    div.find('thead')[0].innerHTML = trs;
                    div.find('tbody')[0].innerHTML = trbs;
//                    setTimeout(refreshRank, 60000);
                },
                "error": function () {
//                    setTimeout(refreshRank, 60000);
                }
            });
        }
        refreshRank();
        refreshScore();
    </script>
@endsection