@extends("backstage.layout.nav")
@section("css")
    <link rel="stylesheet" type="text/css" href="/backstage/css/comment.css?2018080131820">
@endsection
@section("content")
    <div id="Content">
        <div class="inner">
            <div id="Tab">
                {{--<a href="info.html">直播信息</a>--}}
                <a class="on">{{$room->anchor->name}}</a>
            </div>
            <div class="box">
                <div class="get">
                    <div class="title">
                        <div class="input" id="GetInput">
                            <input type="text" name="get" placeholder="请输入360采集地址">
                            <button>开始采集</button>
                        </div>
                    </div>
                    <div class="title" style="top: 43px">
                        <div class="input" id="GetInput2">
                            <input type="text" id="zbb" placeholder="请输入直播吧采集地址">
                            <button>开始采集</button>
                        </div>
                    </div>
                    <ul id="Get" style="top: 86px">
                        <div class="in">
                            </div>
                        <!-- <li>
                            <p class="name">避刃兔<button disabled>已引用</button><span>18-10-10&nbsp;&nbsp;18:00</span></p>
                            <p class="con">「我的女儿...🤩好胖呵呵，...平凡是她的运气🤩」分享！一直和我一起成長的家族朋友</p>
                        </li> -->
                    </ul>
                </div>
                <div class="comment">
                    <div class="title">真实评论</div>
                    <div class="form">
                        <button id="Send">发表</button>
                        <div class="input"><input type="text" name="name" placeholder="请输入昵称" id="NickName"></div>
                        <div class="textarea"><textarea placeholder="请输入内容" id="TextCon"></textarea></div>
                    </div>
                    <ul id="My">
                        <div class="in"></div>
                        <!-- <li>
                            <p class="name">避刃兔<span>18-10-10&nbsp;&nbsp;18:00</span></p>
                            <p class="con">「我的女儿...🤩好胖呵呵，...平凡是她的运气🤩」分享！一直和我一起成長的家族朋友</p>
                        </li> -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("js")
    <script src="https://cdn.bootcss.com/socket.io/2.1.1/socket.io.js"></script>
    <script type="text/javascript">
        var myScroll = true, getScroll = true;
        window.onload = function () { //需要添加的监控放在这里
            GetSocket();
            GetMy();
            $('#Send').click(function(){
                if ($('#NickName').val() == '' || $('#TextCon').val() == '') {
                    alert('请输入昵称和内容');
                    return;
                }
                Send();
                $('#TextCon').val = '';
            });
            $('#Get').scroll(function() {
                // console.log($(this).scrollTop() + ',' + $(this).height() + ',' + $(this).find('.in').height())
                if ($(this).scrollTop() + $(this).height() < $(this).find('.in').height()) {
                    getScroll = false;
                }else{
                    getScroll = true;
                }
            });

            $('#My').scroll(function() {
                // console.log($(this).scrollTop() + ',' + $(this).height() + ',' + $(this).find('.in').height())
                if ($(this).scrollTop() + $(this).height() < $(this).find('.in').height()) {
                    myScroll = false;
                }else{
                    myScroll = true;
                }
            });
        }
        function getTimeType (time) {
            var Time = new Date(time)
            var Year = Time.getFullYear();
            var Month = (Time.getMonth() + 1) < 10 ? '0' + (Time.getMonth() + 1) : (Time.getMonth() + 1);
            var Day = Time.getDate() < 10 ? '0' + Time.getDate() : Time.getDate();
            var Hour = Time.getHours() < 10 ? '0' + Time.getHours() : Time.getHours();
            var Minute = Time.getMinutes() < 10 ? '0' + Time.getMinutes() : Time.getMinutes();

            return Year + '-' + Month + '-' + Day + '&nbsp;&nbsp;' + Hour + ':' + Minute
        }
    </script>
    <script type="text/javascript">
        function GetSocket () {
            $('#GetInput2 button').click(function() {
                if ($('#GetInput2 input').val() == '') {
                    alert('请填写采集地址');
                    return;
                }
                loadZBB();
            });

            $('#GetInput button').click(function(){
                if ($('#GetInput input').val() == '') {
                    alert('请填写采集地址');
                    return;
                }

                $(this).attr('disabled','disabled').html('正在采集');

                var ws = new WebSocket($('#GetInput input').val());
                ws.onmessage = function(event) {
                    var Data = event.data.split('	');
                    // console.log(Data);
                    if (Data.length >= 12 && Data[6].indexOf('span') < 0 && Data[6].indexOf('img') < 0) {
                        var Li = '<li><p class="name">' + Data[3] + '<button onclick="Use(this)">引用</button><span>' + getTimeType(Data[1]*1000) + '</span></p><p class="con">' + Data[6] + '</p></li>';
                        $('#Get .in').append(Li);

                        Send(Data[6],Data[3]);

                        if (getScroll) {
                            $('#Get').scrollTop($('#Get')[0].scrollHeight);
                        }
                    }
                };
            })
        }

        function GetMy () {
            var socket = io.connect('https://ws.aikanqiu.com');
            socket.on('connect', function (data) {
                console.log('connect');
                var mid = '{{'99_'.$room['id']}}';
                var time = Date.parse(new Date())/1000 + '';
                var key = mid + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
                var key = new Uint8Array(encodeUTF8(key));
                var result = md5(key);
                var in_string = Array.prototype.map.call(result,function(e){
                    return (e<16?"0":"")+e.toString(16);
                }).join("");
                var req = {
                    'mid':mid,
                    'isPc':1,
                    'time':time,
                    'verification':in_string
                }
                socket.emit('user_mid', req);
            });

            socket.on('server_send_message', function (data) {
                console.log(data);
                var Li = '<li><p class="name">' + data['nickname'] + '<span>' + getTimeType(data['time']*1000) + '</span></p><p class="con">' + data['message'] + '</p></li>';
                $('#My .in').append(Li);
                if (myScroll) {
                    $('#My').scrollTop($('#My')[0].scrollHeight);
                }
            });
        }

        function Use (obj) {
            var Par = $(obj).parents('li');
            Send(Par.find('p.con').html(),Par.find('p.name').html().split('<button')[0])
        }

        function Send (message,nickname) {
            message = message ? message : $('#TextCon').val();
            nickname = nickname ? nickname : $('#NickName').val();
            var mid = '{{'99_'.$room['id']}}';
            var time = Date.parse(new Date())/1000 + '';
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
                'nickname':nickname,
                'mid':mid,
            };

            $.ajax({
                type: 'POST',
                url: '/app/v120/anchor/chat/post',
                data: req,
                success: function (data) {
                    console.log(data);
                },
            });
        }
    </script>
    <script type="text/javascript">
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
    </script>
    <script type="application/javascript">
        //评论
        function loadZBB() {
            var url = $('#zbb').val();
            var params = url.split('/');
            var id = params[params.length - 1];
            id = id.split('.')[0];
            var sport = params[params.length - 3];
            $.ajax({
                'url':'https://cache.zhibo8.cc/json/2018/'+sport+'/'+id+'_count.htm',
                'success':function (json) {
                    json = JSON.parse(json);

                    var root_num = json['root_num'];
//                    console.log(root_num);
                    //加载最新一页
                    loadZZBComment('https://cache.zhibo8.cc/json/2018/'+sport+'/'+id+'_'+parseInt(root_num/100)+'.htm');
                }
            })
        }

        //评论
        function loadZBBHot() {
            var id = $('#zbb').val();
            $.ajax({
                'url':'https://cache.zhibo8.cc/json/2018/zuqiu/'+id+'_hot.htm',
                'success':function (json) {
                    json = JSON.parse(json);
                    for(var i = 0 ; i < json.length ; i++){
                        var item = json[json.length - 1 -i];
                        console.log('zbb_'+item['id'],item['username'],item['content'],item['createtime'],'text',true);
                    }
                }
            })
        }

        var zbb_last = 0;
        var zbb_array = new Array();
        var zbb_index = 0;
        var zbb_first = true;

        function loadZZBComment(url,nextUrl) {
            $.ajax({
                'url':url,
                'error':function (e) {
                    window.setTimeout(sendZZB, 3000);
                },
                'success':function (json) {
                    json = JSON.parse(json);
                    for(var i = 0 ; i < json.length ; i++){
                        var item = json[json.length - 1 -i];
                        if (item['room'] == 2 && item['id'] > zbb_last) {
                            console.log('room_' + item['room'] + ' zbb_' + item['id'], item['username'], item['content'], item['createtime'], 'text', true);
                            var Li = '<li><p class="name">' + item['username'] + '<button onclick="Use(this)">引用</button><span>' + item['createtime'] + '</span></p><p class="con">' + item['content'] + '</p></li>';
                            $('#Get .in').append(Li);
                            zbb_last = item['id'];
                            zbb_array.push(item);
                        }
                    }

                    if (zbb_first){
                        zbb_array = new Array();
                        zbb_index = 0;
                        zbb_first = false;
                    }

                    if (zbb_array.length > 0){
                        sendZZB();
                    }
                    else{
                        window.setTimeout(sendZZB, 3000);
                    }
                    if (nextUrl){
                        loadZZBComment(nextUrl);
                    }
                }
            })
        }

        function sendZZB() {
            for(var i = 0 ; i < 1; i++){
                if (zbb_index + i < zbb_array.length) {
                    var data = zbb_array[i + zbb_index];
                    Send(data['content'], data['username']);
                }
            }
            zbb_index = zbb_index + 1;
            if (zbb_index >= zbb_array.length - 1){
                zbb_array = new Array();
                zbb_index = 0;
                loadZBB();
                console.log('reload');
            }
            else{
                window.setTimeout(sendZZB, 2000);
            }
        }
    </script>
@endsection
