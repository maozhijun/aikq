@extends('pc.layout.anchor_base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/room.css?{{date('YmdHi')}}">
    <style>
        body {
            padding-top: 60px;
        }
        #TableHead {
            top: 60px;
        }
    </style>
@endsection
@section('content')
    <div id="Content">
        <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<a href="/anchor/">主播</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<span class="on">{{$anchor->name}}</span></div>
        <div class="inner">
            <div id="Info">
                <img src="{{$anchor['icon']}}" onerror="this.src='/img/pc/image_default_head.png'">
                <h1>{{$room['title']}}</h1>
                <?php
                $matchText = '';
                if (isset($match) && $match['status'] > 0){
                    $matchText = "比赛：";
                    if (isset($match['league'])) $matchText .= '【'.$match['league'].'】';
                    if (isset($room_tag) && $room_tag['show_score']) {
                        if (isset($room_tag['h_color'])) {
                            $matchText .= $match['hname']
                                .'<span id="home_color" class="color" style="background: '.$room_tag['h_color'].';"></span>'
                                .' <strong id="match_score">'.$match['hscore'].' - '.$match['ascore'].'</strong> '
                                .'<span id="away_color" class="color" style="background: '.$room_tag['a_color'].';"></span>'
                                . $match['aname'];
                        } else {
                            $matchText .= $match['hname'].' <strong id="match_score">'.$match['hscore'].' - '.$match['ascore'].'</strong> '. $match['aname'];
                        }
                    } else {
                        $matchText .= $match['hname'].' VS '. $match['aname'];
                    }
                }
                ?>
                <p>主播：{{$anchor->name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{!! $matchText !!}<span class="comment">弹幕：<button class="open"></button></p>
            </div>
            <?php
            $url = (isset($room->live_rtmp)&&strlen($room->live_rtmp) > 0)?$room->live_rtmp:$room->live_flv;
            $link = 'https://www.aikq.cc/anchor/room/player/'.$room->id.'.html';
            ?>
            <iframe src="{{$link}}" id="MyFrame"></iframe>
            <div id="Chat">
                <ul>

                </ul>
                <div class="chatbox">
                    @if($room['live_status'] == \App\Models\Anchor\AnchorRoom::kLiveStatusLiving)
                        <button class="send" onclick="send()">发送</button>
                    @else
                        <button class="send" onclick="send()" disabled>发送</button>
                    @endif
                    <p class="name"><input id="nickname" type="text" name="name" placeholder="请输入昵称"></p>
                    <div class="text">
                        <textarea id="text" onkeydown="keySend(event);"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/anchor.js?201808311700"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
            setADClose();
        }

        function scroll () {
            $('#Hot .list').animate({
                scrollLeft: $(this).width()
            }, 2000);//2秒滑动到指定位置
        }
    </script>
    <script src="https://cdn.bootcss.com/socket.io/2.1.1/socket.io.js"></script>
    <script type="text/javascript">
        var nickname = getCookie('ws_nickname');
        if (nickname && nickname.length > 0){
//            $('.name').remove();
            $('#nickname').attr('value',nickname);
            $('#nickname').attr('disabled','disabled')
        }

        function encodeUTF8(s){
            var i,r=[],c,x;
            for(i=0;i<s.length;i++)
                if((c=s.charCodeAt(i))<0x80)r.push(c);
                else if(c<0x800)r.push(0xC0+(c>>6&0x1F),0x80+(c&0x3F));
                else {
                    if((x=c^0xD800)>>10==0) //对四字节UTF-16转换为Unicode
                        c=(x<<10)+(s.charCodeAt(++i)^0xDC00)+0x10000,
                                r.push(0xF0+(c>>18&0x7),0x80+(c>>12&0x3F));
                    else r.push(0xE0+(c>>12&0xF));
                    r.push(0x80+(c>>6&0x3F),0x80+(c&0x3F));
                };
            return r;
        };

        function md5(data){
            /**************************************************
             Author：次碳酸钴（admin@web-tinker.com）
             Input：Uint8Array
             Output：Uint8Array
             **************************************************/
            var i,j,k;
            var tis=[],abs=Math.abs,sin=Math.sin;
            for(i=1;i<=64;i++)tis.push(0x100000000*abs(sin(i))|0);
            var l=((data.length+8)>>>6<<4)+15,s=new Uint8Array(l<<2);
            s.set(new Uint8Array(data.buffer)),s=new Uint32Array(s.buffer);
            s[data.length>>2]|=0x80<<(data.length<<3&31);
            s[l-1]=data.length<<3;
            var params=[
                [function(a,b,c,d,x,s,t){
                    return C(b&c|~b&d,a,b,x,s,t);
                },0,1,7,12,17,22],[function(a,b,c,d,x,s,t){
                    return C(b&d|c&~d,a,b,x,s,t);
                },1,5,5,9,14,20],[function(a,b,c,d,x,s,t){
                    return C(b^c^d,a,b,x,s,t);
                },5,3,4,11,16,23],[function(a,b,c,d,x,s,t){
                    return C(c^(b|~d),a,b,x,s,t);
                },0,7,6,10,15,21]
            ],C=function(q,a,b,x,s,t){
                return a=a+q+(x|0)+t,(a<<s|a>>>(32-s))+b|0;
            },m=[1732584193,-271733879],o;
            m.push(~m[0],~m[1]);
            for(i=0;i<s.length;i+=16){
                o=m.slice(0);
                for(k=0,j=0;j<64;j++)m[k&3]=params[j>>4][0](
                        m[k&3],m[++k&3],m[++k&3],m[++k&3],
                        s[i+(params[j>>4][1]+params[j>>4][2]*j)%16],
                        params[j>>4][3+j%4],tis[j]
                );
                for(j=0;j<4;j++)m[j]=m[j]+o[j]|0;
            };
            return new Uint8Array(new Uint32Array(m).buffer);
        };

        //    var socket = io.connect('http://bj.xijiazhibo.cc');
//        var socket = io.connect('http://localhost:6001');
        var socket = io.connect('https://ws.aikanqiu.com');
        socket.on('connect', function (data) {
            console.log('connect');
            var mid = '{{'99_'.$room_id}}';
            var time = Date.parse( new Date())/1000 + '';
            var key = mid + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
            var key = new Uint8Array(encodeUTF8(key));
            var result = md5(key);
            var in_string = Array.prototype.map.call(result,function(e){
                return (e<16?"0":"")+e.toString(16);
            }).join("");
            var nickname = getCookie('ws_nickname');
            var req = {
                'mid':mid,
                'isPc':1,
                'time':time,
                'verification':in_string,
                'nickname':nickname
            }
            socket.emit('user_mid', req);
        });

        socket.on('server_send_message', function (data) {
            console.log(data);
            $('#Chat ul').append('<li><span>'+data['nickname']+'：</span>'+data['message']+'</li>');
            $("#Chat ul").scrollTop($("#Chat ul")[0].scrollHeight);
        });
        socket.on('server_match_change', function (data) {
//            console.log(data);
            $('#match_score').html(data['hscore'] + " - " + data['ascore']);
        });
        socket.on('server_color_change', function (data) {
//            console.log(data);
            $('#home_color')[0].style.background = data['h_color'];
            $('#away_color')[0].style.background = data['a_color'];
        });

        var hasHistory = false;
        socket.on('server_history_message', function (messages) {
            if (hasHistory){
                return;
            }
            hasHistory = true;
//            console.log(messages);
            for (var i = 0 ; i < messages.length ; i++){
                var data = messages[i];
                $('#Chat ul').append('<li><span>'+data['nickname']+'：</span>'+data['message']+'</li>');
                $("#Chat ul").scrollTop($("#Chat ul")[0].scrollHeight);
            }
        });

        function send() {
            var message = document.getElementById('text').value;
            var time = Date.parse( new Date())/1000 + '';
            var key = message + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
            var key = new Uint8Array(encodeUTF8(key));
            var result = md5(key);
            var in_string = Array.prototype.map.call(result,function(e){
                return (e<16?"0":"")+e.toString(16);
            }).join("");
            var req = {
                'message':message,
                'time':time,
                'verification':in_string,
            };
            var nickname = getCookie('ws_nickname');
            if (nickname && nickname.length > 0){

            }
            else{
                nickname = document.getElementById('nickname').value;
            }
            if (nickname.length > 0){
                setCookie('ws_nickname',nickname,7);
                req = {
                    'message':message,
                    'time':time,
                    'verification':in_string,
                    'nickname':nickname,
                    'mid':'{{'99_'.$room_id}}',
                };
            }
            $.ajax({
                type: 'POST',
                url: '/app/v120/anchor/chat/post',
                data: req,
                success: function (data) {
                    console.log(data);
                },
            });
            document.getElementById('text').value = '';
//            socket.emit('user_send_message', req);
        }

        function keySend(event) {
            if (event.keyCode == 13) {
                send();
                setTimeout(function () {
                    document.getElementById('text').value = ''
                },10);
            }
        }

        function setCookie(name, value, days) {
            days = /^\d+$/.test(days) ? days : 30;
            var exp = new Date();
            exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = name + "=" + encodeURIComponent(value) + ";path=/;expires=" + exp.toGMTString();
        }

        function getCookie(name) {
            var arr , reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
            if(arr = document.cookie.match(reg)) {
                return decodeURIComponent(arr[2]);
            } else {
                return null;
            }
        }
    </script>
@endsection