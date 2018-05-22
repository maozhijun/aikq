@extends('mobile.fifa.base')
@section('content')
    <div class="bigBg" style="background: url({{isset($team['bg_img'])?$team['bg_img']:'https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike80%2C5%2C5%2C80%2C26/sign=d7f09c74a08b87d6444fa34d6661435d/203fb80e7bec54e7523d345bb8389b504ec26aa5.jpg'}}) no-repeat center; background-size: cover;">
        <div class="icon"  style="background: url({{$team['icon']}}) no-repeat center #fff; background-size: contain"></div>
    </div>
    <div id="Tab">
        <p class="item on" for="Match">赛程赛果</p>
        <p class="item" for="Player">教练球员</p>
        <p class="item" for="Info">详细介绍</p>
    </div>
    <div class="matchList" id="Match">
        @foreach($matches as $match)
            @component('mobile.fifa.match_cell',['match'=>$match])
            @endcomponent
        @endforeach
    </div>
    <ul id="Player" style="display: none;">
        @if(count($lineup['coach']) > 0)
            <li>
                <p class="coach">教</p>
                <img src="{{env('CDN_URL')}}/img/mobile/fifa/image_player_n.jpg">
                <p class="name">{{$lineup['coach'][0]['name']}}</p>
            </li>
        @endif
        @foreach($lineup['lineup'] as $player)
            <li>
                <p class="number">{{$player['num']}}</p>
                <img src="{{$player['icon']}}" onerror="this.src='{{env('CDN_URL')}}/img/mobile/fifa/image_player_n.jpg'">
                <p class="name">{{$player['name']}}</p>
                <p class="site">{{$player['pos']}}</p>
            </li>
        @endforeach
    </ul>
    <div id="Info" style="display: none;">
        <div class="in">
            <p>{{$team['describe']}}</p>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/teamEnd.js?time=201803030002"></script>
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
                            break;
                        }
                    }
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

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/teamEnd.css">
@endsection
