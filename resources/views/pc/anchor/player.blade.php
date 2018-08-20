<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots"content="nofollow">
    <meta name="referrer" content="no-referrer">
    {{--<title>爱看球-JRS|JRS直播|NBA直播|NBA录像|CBA直播|英超直播|西甲直播|低调看|直播吧|CCTV5在线</title>--}}
    {{--<meta name="Keywords" content="JRS,JRS直播,NBA直播,NBA录像,CBA直播,英超直播,西甲直播,足球直播,篮球直播,低调看,直播吧,CCTV5在线,CCTV5+">--}}
    {{--<meta name="Description" content="爱看球是一个专业为球迷提供免费的NBA,CBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大体育赛事直播、解说平台，无广告，无插件，高清，直播线路多">--}}
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/css/pc/style.css?rd=2018">
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/css/pc/player.css?rd=20180306">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{$cdn}}/img/pc/ico.ico" href="{{$cdn}}/img/pc/ico.ico">
</head>
<body scroll="no">
<div class="player_content" id="MyFrame">
    {{--<p class="noframe" style="display: none;">距离比赛还有 <b>08:23</b><img class="code" src="/img/pc/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>--}}
</div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript" src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js"></script>
<script type="text/javascript" src="{{$cdn}}/js/public/pc/ckplayer/ckplayer.js?timd=201808182005"></script>
<script src="https://cdn.bootcss.com/socket.io/2.1.1/socket.io.js"></script>
<script type="text/javascript">
    function isMobileWithJS() {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1; //android终端或者uc浏览器
        var isiPhone = u.indexOf('iPhone') > -1; //是否为iPhone或者QQ HD浏览器
        var isiPad = u.indexOf('iPad') > -1; //是否iPad
        return (isAndroid || isiPhone || isiPad) ? '1' : '';
    }
</script>

<script type="text/javascript">
    <?php //$host = '//localhost:9090'; $cnd = ''; ?>
    function ShareWarm (Text) {
        var P = document.createElement('p');
        P.id = 'ShareWarm';
        P.innerHTML = Text;
        document.body.appendChild(P)
    }
    window.host = window.location.host;
//{{--    window.isMobile = '{{\App\Http\Controllers\Controller::isMobileUAgent($_SERVER['HTTP_USER_AGENT'])}}';--}}
            window.isMobile = isMobileWithJS();
    window.cdn_url = '{{$cdn}}';
    if (window.cdn_url && window.cdn_url != "") {
        window.cdn_url = (location.href.indexOf('https://') != -1 ? 'https:' : 'http:') + window.cdn_url;
    }
    //window.CKHead = (location.href.indexOf('https://') != -1 ? 'https:' : 'http:') + '{{$cdn}}/js/public/pc/ckplayer/';
</script>
<script src="https://cdn.bootcss.com/socket.io/2.1.1/socket.io.js"></script>
<script type="text/javascript" src="{{$cdn}}/js/public/pc/anchor_player.js?rd=201807231742"></script>
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    });
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        LoadVideo();
    }
    function showDownload() {
        var ua = navigator.userAgent;
        var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
            isIphone =!ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
            isAndroid = ua.match(/(Android)\s+([\d.]+)/),
            isMobile = isIphone || isAndroid || ipad;

        //判断

        if(isMobile){
            // var ADDHtml = '<div class="publicAd" style="position: fixed;bottom: 0;left: 0;right: 0;">' +
            // 			  '<button style="width: 50px; height: 50px; background: url(img/icon_close_btn_white.png) no-repeat center rgba(0,0,0,0.3); background-size: 24px;; position: absolute; right: 0; top: 0;"></button>' +
            // 			  '<a href="downloadPhone.html" target="_top"><img src="img/image_ad_wap.jpg" width="100%"></a>' +
            // 			  '</div>';
            // $('#MyFrame').after(ADDHtml)

            // $('.publicAd button').click(function () {
            // 	$(this).parents('.publicAd').remove();
            // })
            var Warm = '<div id="WaitWarm">视频加载需要时间，如超过10秒无画面请刷新</div>';
            $('#MyFrame').after(Warm)
        }
    }
    showDownload();
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

    //    var socket = io.connect('http://bj.xijiazhibo.cc');
    //var socket = io.connect('http://localhost:6001');
    var socket = io.connect('https://ws.aikq.cc');
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
        var req = {
            'mid':mid,
            'time':time,
            'verification':in_string,
        }
        socket.emit('user_mid', req);
    });

    socket.on('server_send_message', function (data) {
        if (data['type'] && data['type'] == 99){

        }
        else {
            popText(data['message'], data['nickname']);
        }
    });
</script>
</html>