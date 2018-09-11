@extends('mobile.fifa.base')
@section('content')
    <div id="Topbar">
        <a href="https://m.aikanqiu.com/">直播</a>
        <a href="https://m.aikanqiu.com/live/subject/videos/all/1.html">录像</a>
        <a href="https://shop.liaogou168.com/article/recommends">推荐</a>
        <a class="on">世界杯</a>
    </div>
    <div id="roundTab">
        @foreach($stages as $stage)
            <?php
            if(isset($stage['groupMatch'])){
                $id = 'group';
            }
            else{
                $id = $stage['id'];
            }
            ?>
            <p @if($stage['status'] == 1)class="on"@endif for="{{$id}}">{{$stage['name']}}</p>
        @endforeach
    </div>
    @foreach($stages as $stage)
        <?php
        if(isset($stage['groupMatch'])){
            $id = 'group';
        }
        else{
            $id = $stage['id'];
        }
        ?>
        @if(isset($stage['groupMatch']))
            <?php
            $groups = $stage['groupMatch'];
            ?>
            <div id="{{$id}}" class="matchList" @if($stage['status'] == 0)style="display: none" @endif>
                <div class="groupBox">
                    @foreach($groups as $key=>$group)
                        <p for="{{$key}}" @if($key == 'A')class="on"@endif><span>{{$key}}组</span></p>
                    @endforeach
                </div>
                @foreach($groups as $key=>$group)
                    <div class="matchList" id="{{$key}}" @if($key != 'A')style="display: none;"@endif>
                        <?php
                        //比赛状态分类
                        $notStart = array();
                        $ing = array();
                        $end = array();
                        foreach ($group['matches'] as $match){
                            if ($match['status'] == 0){
                                $notStart[] = $match;
                            }
                            elseif ($match['status'] == -1){
                                $end[] = $match;
                            }
                            else{
                                $ing[] = $match;
                            }
                        }
                        ?>
                        <table>
                            <thead>
                            <tr>
                                <th>排名</th>
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
                                <tr>
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
                        <div class="matches_div">
                            <div class="separated live" style="display: none;">比赛中</div>
                            @foreach($ing as $match)
                                @component('mobile.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </div>
                        <div class="matches_div">
                            <div class="separated noStart" style="display: none;">未开始</div>
                            @foreach($notStart as $match)
                                @component('mobile.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </div>
                        <div class="matches_div">
                            <div class="separated end" style="display: none;">已结束</div>
                            @foreach($end as $match)
                                @component('mobile.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="matchList" id="{{$id}}" @if($stage['status'] == 0)style="display: none" @endif>
                <?php
                $matches = $stage['matches'];
                //比赛状态分类
                $notStart = array();
                $ing = array();
                $end = array();
                foreach ($matches as $match){
                    if ($match['status'] == 0){
                        $notStart[] = $match;
                    }
                    elseif ($match['status'] == -1){
                        $end[] = $match;
                    }
                    else{
                        $ing[] = $match;
                    }
                }
                ?>
                <div class="matches_div">
                    <div class="separated live" style="display: none;">比赛中</div>
                    @foreach($ing as $match)
                        @component('mobile.fifa.match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                </div>
                <div class="matches_div">
                    <div class="separated noStart" style="display: none;">未开始</div>
                    @foreach($notStart as $match)
                        @component('mobile.fifa.match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                </div>
                <div class="matches_div">
                    <div class="separated end" style="display: none;">已结束</div>
                    @foreach($end as $match)
                        @component('mobile.fifa.match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
    @component('mobile.fifa.base_bottom',['index'=>0])
    @endcomponent
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/home.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <style>
        #roundTab{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/image_bg_nav_n.jpg) no-repeat center; background-size: cover;!important;
        }
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
    </style>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/home.js?time=201803030002"></script>
    <script type="text/javascript">
        function refreshHead() {
            var divs = $('div.matches_div');
            for (var i = 0 ; i < divs.length ; i++){
                if (divs[i].getElementsByTagName('a').length > 0){
                    $(divs[i]).find('div.separated')[0].style['display'] = '';
                }
                else{
                    $(divs[i]).find('div.separated')[0].style['display'] = 'none';
                }
            }
        }
        refreshHead();
    </script>
    <script type="text/javascript">
        function refreshMatch() {
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var strDate = date.getDate();
            if (month >= 1 && month <= 9) {
                month = "0" + month;
            }
            if (strDate >= 0 && strDate <= 9) {
                strDate = "0" + strDate;
            }
            var url = "http://match.liaogou168.com/static/schedule/"+year+month+strDate+"/1/all.json";
            $.ajax({
                "url": url,
                dataType: "jsonp",
                "success": function (json) {
                    var matches = json['matches'];
                    for(var i = 0 ; i < matches.length ; i++){
                        var match = matches[i];
                        var a = $('a#match_cell_' + match['mid']);
                        if (a && a.length > 0){
                            a.find('.hscore')[0].innerHTML=match['hscore'];
                            a.find('.ascore')[0].innerHTML=match['ascore'];
                            a.find('.hscorehalf')[0].innerHTML=match['hscorehalf'];
                            a.find('.ascorehalf')[0].innerHTML=match['ascorehalf'];
                            a.find('.hyellow')[0].innerHTML=match['h_yellow'];
                            a.find('.hred')[0].innerHTML=match['h_red'];
                            a.find('.ayellow')[0].innerHTML=match['a_yellow'];
                            a.find('.ared')[0].innerHTML=match['a_red'];
                            //切换比赛状态
                            var status = a[0].getAttribute('m_status');
                            a[0].setAttribute('m_status',match['status']);
                            if (status != match['status']){
                                if (match['status'] > 0){
                                    $(a.parent().parent().find('.matches_div')[0]).append(a);
                                }
                                else if(match['status'] == -1){
                                    $(a.parent().parent().find('.matches_div')[2]).append(a);
                                }
                                else if(match['status'] == 0){
                                    $(a.parent().parent().find('.matches_div')[1]).append(a);
                                }
                            }
                            var time = $('div#time_'+ match['mid']);
                            if (time && time.length > 0){
                                var p = time.find('p')[1];
                                p.innerHTML = '';
                                if (match['status'] == -1){
                                    p.innerHTML = '<p class="end"></p>';
                                }
                                else if(match['status'] > 0){
                                    p.innerHTML = '<p class="live"><span class="minute">123</span></p>';
                                }
                            }
                        }
                    }
                    refreshHead();
                    setTimeout(refreshMatch, 60000);
                },
                "error": function () {
                    setTimeout(refreshMatch, 60000);
                }
            });
        }
        refreshMatch();
    </script>
@endsection