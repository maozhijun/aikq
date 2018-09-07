var CKHead = '/js/public/pc/ckplayer/';
var maxTimeOut = 0, ad_time = 0;
// var ad_l = '/img/pc/ankanqiu_six.jpg', ad_d = '/img/pc/ankanqiu_six.jpg', ad_z = '/img/pc/ankanqiu_six.jpg', ad_w = '/img/pc/ankanqiu_six.jpg';
var ad_l = '/img/pc/demo.jpg', ad_d = '/img/pc/demo.jpg', ad_z = '/img/pc/demo.jpg', ad_w = '/img/pc/demo.jpg';
var cd = '/img/pc/image_qr_868.jpg', cd_name = 'kanqiu868', cd_text = '与球迷赛事交流，乐享高清精彩赛事！';
var WXCodeRun = false, firstShowCode = false;
var active_text = '';//'加微信{font color="#e3f42c"}【fs188fs】{/font}\n球迷乐享超清精彩赛事';
var active_code = '';//'/img/pc/code.jpg';//'/img/pc/i_wx_code.jpg';
var valid_code = '8888', show_ad = true, matchTime, matchStatus, errorRepeat = 1;

$.ajax({
    "url": "/json/dd_image/images.json?time=" + (new Date()).getTime(),
    "success": function (json) {
        if (json) {
            if (json.l) ad_l = json.l;
            if (json.d) ad_d = json.d;
            if (json.z) ad_z = json.z;
            if (json.w) ad_w = json.w;
            if (json.cd) cd = json.cd;
            if (json.cd_name) cd_name = json.cd_name;
            if (json.cd_text) cd_text = json.cd_text;
            if (json.code) valid_code = json.code;
        }
        var code = getCookie('LIVE_HD_CODE_KEY');
        if (code == valid_code) {
            ad_time = 0;//已输入验证码，不出广告
        }
    }
});
//获取链点参数
function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    }
    var rs = new RegExp("([\?&])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
    if (rs) {
        return decodeURI(rs[3]);
    } else {
        return '';
    }
}
//判断手机
function isPhone() {
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        return true;
    }else{
        return false;
    }
}
//判断微信
function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}
function LoadVideo () {
    // if (isWeiXin() && top.location.href.indexOf('aikq.cc') != -1) {
    //     //在这里写如果是微信的时候的状态
    //     $('#MyFrame').html('<p class="noframe">请使用 <b>浏览器</b> 打开<img class="code" src="/img/pc/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>')
    //     return;
    // }
    if (isPhone() && ad_time > 0) {
        //如果是手机，加载5秒广告
        $('body').append('<div id="PhoneAD"><img src="' + ad_w + '"><p class="time">广告剩余：<b>5</b> 秒</p></div>');
        var ADRun = setInterval(function(){
            var Val = parseInt($('#PhoneAD b').html());
            if (Val > 0) {
                $('#PhoneAD b').html(Val - 1);
            }else{
                clearInterval(ADRun);
                $('#PhoneAD').remove();
            }
        },1000);
    }
    playerLink();
}

function LoadCK (Link){ //m3u8
    if ((Link.indexOf('http://') == 0 || Link.indexOf('https://') == 0) && IsPC() && navigator.userAgent.indexOf('Safari') == -1) {
        Link = encodeURIComponent(Link)
    }
    var flashvars={
        s:4,
        f:CKHead + 'm3u8.swf',
        a:Link,
        lv:1,
        c:0,
        p:1,
        l: ad_l,
        d: ad_d,
        z: ad_z,
        t: maxTimeOut > 0 ? 0 : ad_time,
        loaded:'loadHandler'
    }
    if (flashvars.t == 0) {
        flashvars.l = "";
        flashvars.d = "";
        flashvars.z = "";
    }
    var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
    var video=[''+Link+'->video/mp4'];
    CKobject.embed( CKHead + 'ckplayer.swf','MyFrame','ckplayer_a1','100%','100%',false,flashvars,video,params);
    if (isPhone()) {
        $('video').attr('playsinline','true');
        $('video').attr('x-webkit-airplay','true');
        $('video').attr('webkit-playsinline','true');
        $('video').attr('x5-playsinline','true');

        $('video').attr('muted','');
        $('video').attr('autoplay','autoplay');
        $('video').attr('x5-autoplay','autoplay');
        $('video').attr('x-webkit-autoplay','autoplay');
        $('video').attr('webkit-autoplay','autoplay');
    }
}

function LoadFlv (Link){ //flv
    if (Link.indexOf('http://') == 0 || Link.indexOf('https://') == 0) {
        Link = encodeURIComponent(Link);
    }
    var flashvars={
        f:''+Link+'',
        lv:1,
        c:0,
        p:1,
        l: ad_l,
        d: ad_d,
        z: ad_z,
        t:maxTimeOut > 0 ? 0 : ad_time,
        loaded:'loadHandler'
    }
    if (flashvars.t == 0) {
        flashvars.l = "";
        flashvars.d = "";
        flashvars.z = "";
    }
    var video=[''+Link+'->video/mp4','http://www.ckplayer.com/webm/0.webm->video/webm','http://www.ckplayer.com/webm/0.ogv->video/ogg'];
    CKobject.embed( CKHead + 'ckplayer.swf','MyFrame','ckplayer_a1','100%','100%',false,flashvars,video)
}

function LoadRtmp (Link){ //rtmp
    var flashvars = {
        f:Link,
        lv:1,
        c:0,
        p:1,
        l: ad_l,
        d: ad_d,
        z: ad_z,
        t:maxTimeOut > 0 ? 0 : ad_time,
        loaded:'loadHandler'
    }
    if (flashvars.t == 0) {
        flashvars.l = "";
        flashvars.d = "";
        flashvars.z = "";
    }
    var params = {
        allowFullScreen: true,
        allowScriptAccess: "always",
        bgcolor: "#000000"
    };
    var attrs = {
        name: "ckplayer"
    };
    var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
    var video=[''+Link+'->video/mp4'];
    CKobject.embed( CKHead + 'ckplayer.swf?url=','MyFrame','ckplayer_a1','100%','100%',false,flashvars,video,params);
}

function LoadMP4 (Link){ //录像
    if ((Link.indexOf('http://') == 0 || Link.indexOf('https://') == 0) && IsPC() && navigator.userAgent.indexOf('Safari') == -1) {
        Link = encodeURIComponent(Link)
    }
    var flashvars={
        f:Link,
        c:0,
        p:1,
        l: maxTimeOut > 0 ? '' : 'img/demo.jpg',
        d:'img/demo.jpg',
        z:'img/demo.jpg',
        t: maxTimeOut > 0 ? 0 : 5,
        loaded:'loadHandler'
    };
    if (flashvars.t == 0) {
        flashvars.l = "";
        flashvars.d = "";
        flashvars.z = "";
    }
    var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
    var video=[''+Link+'->video/mp4'];
    CKobject.embed( CKHead + 'ckplayer.swf','MyFrame','ckplayer_a1','100%','100%',false,flashvars,video,params);

    if (isPhone()) {
        $('video').attr('playsinline','true')
    }
}

function LoadIframe (Link) { //iframe
    var Frame = document.createElement('iframe');
    Frame.setAttribute('allowfullscreen','true');
    Frame.setAttribute('scrolling','no');
    Frame.setAttribute('frameborder','0');
    Frame.width = '100%';
    Frame.height = '100%';
    Frame.src = Link;
    document.getElementById('MyFrame').appendChild(Frame)
}

function LoadPPTV (Link) { //PPTV
    document.getElementById('MyFrame').innerHTML = '<embed src="' + Link + '" quality="high" width="100%" height="100%" bgcolor="#000000" align="middle" allowScriptAccess="always" allownetworking="all" allowfullscreen="true" type="application/x-shockwave-flash" wmode="direct" />';
}

function LoadSports365 (ID) { //Sport365
    document.getElementById('MyFrame').innerHTML = '<object id="BridgeMovie" width="100%" height="100%" type="application/x-shockwave-flash" data="http://sportstream365.com/getZone/VideoPlayerSportstream.swf?tag=1">' +
        '<param name="menu" value="true">' +
        '<param name="wmode" value="window">' +
        '<param name="allowFullScreen" value="true">' +
        '<param name="AllowScriptAccess" value="always">' +
        '<param name="flashvars" value="ZonePlayGameId=' + ID + '&amp;scaleMode=scaleAll&amp;userID=0&amp;videoID=' + ID + '&amp;matchName=1&amp;startImmediately=true&amp;gameId=' + ID + '&amp;lng=ru&amp;sport=0&amp;ref=36">' +
        '</object>';
}

function LoadTV (Link) { //斗鱼、企鹅、虎牙
    document.getElementById('MyFrame').innerHTML = '<embed width="100%" height="100%" allownetworking="all" allowscriptaccess="always" src="' + Link + '" quality="high" bgcolor="#000" wmode="window" allowfullscreen="true" allowFullScreenInteractive="true" type="application/x-shockwave-flash">';
}

function ShareWarm (Text) {
    var P = document.createElement('p');
    P.id = 'ShareWarm';
    P.innerHTML = Text;
    document.body.appendChild(P)
}

function ckmarqueeadv(){return '免费看球用爱看球：<a href="http://www.lg310.com" target="_blank">akq.cc</a> 进千人球迷群领红包 加微信<span>fs188fs</span>'}

function CloseLoading () {
    document.getElementById('MyFrame').innerHTML = '';
}


//监听相关
function loadHandler(){
    if(CKobject.getObjectById('ckplayer_a1').getType()){
        console.log('播放器已加载，调用的是HTML5播放模块');
        CKobject.getObjectById('ckplayer_a1').addListener('play',playHandler);
        // CKobject.getObjectById('ckplayer_a1').addListener('buffer',bufferHandler);
        CKobject.getObjectById('ckplayer_a1').addListener('error',errorHandler);
        // CKobject.getObjectById('ckplayer_a1').addListener('paused',pausedHandler);
    }
    else{
        console.log('播放器已加载，调用的是Flash播放模块');
        CKobject.getObjectById('ckplayer_a1').addListener('play','playHandler');
        // CKobject.getObjectById('ckplayer_a1').addListener('buffer','bufferHandler');
        CKobject.getObjectById('ckplayer_a1').addListener('error','errorHandler');
        // CKobject.getObjectById('ckplayer_a1').addListener('coordinateChange','coordinateHandler');
        CKobject.getObjectById('ckplayer_a1').addListener('textBoxShow','textBoxShowHandler');
    }
}

function coordinateHandler (b){
    var Status = CKobject.getObjectById('ckplayer_a1').getStatus();
    if (Status.controlBarShow) {
        CKobject.getObjectById('ckplayer_a1').textBoxTween('AttWX',[['y',0,-32,0.4]]);
    }else{
        CKobject.getObjectById('ckplayer_a1').textBoxTween('AttWX',[['y',0,32,0.4]]);
    }
}

function playHandler (){
    //PC时添加心跳请求
    if (!isPhone() && !WXCodeRun) {
        console.log(WXCodeRun);
        //checkActive();
        WXCodeRun = setInterval(function(){//每5秒请求一次服务器查看有没有更新 活动信息
            //checkActive();
        }, 15 * 60 * 1000);
    }
    CKobject.getObjectById('ckplayer_a1')._V_.muted = false; //安卓有可能出现默认静音，这里修改一下静音选项
    $('#WaitWarm').remove()
}

function bufferHandler (num) {
    if (num > 100 || num < 0) {
        console.log(num)
        playerLink();
    }
}

function errorHandler () {
    var nowTime = (new Date()).getTime() / 1000;

    if (matchStatus == 0 || matchTime > nowTime) {
        countdownHtmlNew();
    }
    return;
    // if (maxTimeOut > 5) {
    // return;
    // }
    // maxTimeOut++;
    // console.log('error，重新请求链接');
    // playerLink();
}

//获取是S还是非S
function GetHttp () {
    if (location.href.indexOf('https://') != -1) {
        return 'https://';
    }else{
        return 'http://';
    }
}
function countdownHtmlNew() {
    var mTime = matchTime;
    var now = (new Date()).getTime() / 1000;
    if (now >= mTime && errorRepeat < 2) {//现在大于等比赛时间，再请求一次
        errorRepeat++;
        playerLink();
        return;
    }
    if (now >= mTime && errorRepeat >= 2) {
        return;
    }
    var hour = Math.floor( (mTime - now) / (60 * 60) );
    var minute = Math.floor( (mTime - now - hour * 60 * 60) / 60 );
    var second = Math.floor( (mTime - now - hour * 60 * 60 - minute * 60) );

    hour = hour < 10 ? "0" + hour : hour;
    minute = minute < 10 ? "0" + minute : minute;
    second = second < 10 ? "0" + second : second;

    var html = hour + ":" + minute + ":" + second;
    if ($("#MyFrame p.noframe").length == 0) {
        $("#MyFrame").html('<p class="noframe" style="display: none;">距离比赛还有 <b>' + html + '</b><img class="code" src="' + cd + '">加微信 <b>' + cd_name + '</b><br/>' + cd_text + '</p>');
    } else {
        $("#MyFrame p.noframe").show().find('b:first').html(html);
    }
    setTimeout(countdownHtmlNew, 1000);
}
function countdownHtml(hour_html, minute_html, second_html) {
    var hour = '00';
    var minute = '00';
    var second = '00';
    if (hour_html) {
        hour = $(hour_html).html();
        hour = parseInt(hour);
        hour = hour < 10 ? ('0' + hour) : hour;
    }
    if (minute_html) {
        minute = $(minute_html).html();
        minute = minute < 10 ? ('0' + minute) : minute;
    }
    if (second_html) {
        second = $(second_html).html();
        second = second < 10 ? ('0' + second) : second;
    }
    var time_html = hour + ":" + minute + ":" + second;
    //$("#MyFrame p.noframe img").attr('src', ad_w);
    $("#MyFrame p.noframe").show().find('b:first').html(time_html);
    setInterval(countdown, 1000);
}
function countdown() {
    var time = $("#MyFrame p.noframe b:first").html();
    if (time) {
        var times = time.split(':');
        if (times.length == 3) {
            var hour = parseInt(times[0]);
            var minute = parseInt(times[1]);
            var second = parseInt(times[2]);
            second--;
            if (second < 0) {
                second = 59;
                minute--;
                if (minute < 0 && hour > 0) {
                    if (hour > 0) {
                        minute = 59;
                        hour--;
                    } else {
                        minute = 0;
                    }
                }
            }
            if (hour == 0) location.reload();
            hour = hour < 10 ? ('0' + hour) : hour;
            minute = minute < 10 ? ('0' + minute) : minute;
            second = second < 10 ? ('0' + second) : second;
            var time_html = hour + ":" + minute + ":" + second;
            $("#MyFrame p.noframe b:first").html(time_html);
        }
    }
}
//获取播放地址
function PlayVideoShare (cid, type){
    var url;
    // if (type == 9) {
    //     url = GetHttp() + host + '/match/live/url/channel/hd/' + cid;
    // } else {
    if (isPhone()) {
        url = GetHttp() + host + '/match/live/url/channel/mobile/' + cid + '.json';
    } else {
        url = GetHttp() + host + '/match/live/url/channel/' + cid + '.json';
    }
    // }
    url = url + '?time=' + (new Date()).getTime();
    $.ajax({
        url: url,
        type:'GET',
        dataType:'json',
        success:function(data){
            if (data.code == 0){
                //CloseLoading();
                var match = data.match;
                matchStatus = match.status;
                matchTime = match.time;
                var show_live = match.show_live;
                if (window.isMobile && data.platform && data.platform == 2 && (show_live || match.status == 0)) {//如果是PC端的线路，未开始比赛或者在直播中，则提示
                    $('#MyFrame').html('<p class="noframe">该比赛暂无手机信号，请使用<b>电脑浏览器</b> 打开<img class="code" src="/img/pc/image_qr_868.jpg">加微信 <b>kanqiu868</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>')
                    return;
                }
                if(!show_live){
                    if (match.status == 0) {
                        //countdownHtml(match.hour_html, match.minute_html, match.second_html);
                        countdownHtmlNew();
                    }
                    return;
                }else if(show_live){
                    if (data.ad && data.ad == 2) {
                        show_ad = false;
                    }
                    //高清线路 处理
                    if (data.type == 9 || (data.h_playurl && data.h_playurl.length > 0)) {
                        var code = getCookie('LIVE_HD_CODE_KEY');
                        if (code == valid_code) {//已获取高清码
                            show_ad = false;
                            closeCode();
                            data.playurl = data.h_playurl;
                        } else {
                            showCode();
                        }
                    }
                    // if (data.type == 9 && !data.hd) {
                    //     showCode();
                    // } else if (data.hd) {//已获取高清码
                    //     show_ad = false;
                    //     closeCode();
                    // }
                    //高清线路 处理
                    if (data.type == 1) { //如果是365，直接播放，不使用链接
                        var ID = data.id;
                        LoadSports365(ID)
                    } else if (data.type == 2) {
                        var Link = getLink(data);
                        if (data.playurl) {
                            LoadIframe(Link);
                        } else {
                            CheckPlayerType(Link,0);
                        }
                    } else{ //其他，获取播放地址和播放方式
                        var Link = getLink(data);
                        var PlayType = data.player;
                        if (PlayType == 11) { //iframe
                            LoadIframe(Link)
                        }else if (PlayType == 12) { //ckplayer
                            CheckPlayerType(Link,1);
                        }else if (PlayType == 13) { //m3u8
                            LoadCK (Link)
                        }else if (PlayType == 14) { //flv
                            LoadFlv (Link);
                        }else if (PlayType == 15) { //rtmp
                            LoadRtmp (Link)
                        } else if (PlayType == 17) {
                            LoadClappr(Link);
                        } else if (PlayType == 18) {
                            LoadMP4(Link);
                        }else if(PlayType == 100){//腾讯体育专用
                            $.ajax({
                                url: Link,
                                dataType: "jsonp",
                                success: function (data) {
                                    if(data.playurl) {
                                        Link = data.playurl;
                                        if (isMobileWithJS()) {
                                            Link = Link.replace('.flv', '.m3u8');
                                            LoadCK(Link);
                                        }
                                        else {
                                            if (Link.indexOf('.flv') != -1) {
                                                LoadFlv(Link);
                                            }
                                            else{
                                                LoadCK(Link);
                                            }
                                        }
                                    }
                                    else{
                                        document.getElementById('MyFrame').innerHTML = '<p class="loading">暂无直播信号</p>';
                                    }
                                }
                            });
                        }
                        else{
                            CheckPlayerType(Link,0)
                        }
                    }
                }
            }else{
                document.getElementById('MyFrame').innerHTML = '<p class="loading">暂无直播信号</p>';
            }
        }
    })
}

function PlayVideoSubject (cid, type){
    var isPhone = window.isMobile;

    var mobil = isPhone ? '/mobile' : '';
    var url;
    if (type == 'video' || type == 'specimen') {
        var cidStr = cid + '';
        var first = cidStr.substr(0, 2);
        var second = cidStr.substr(2, 4);
        url = '/live/subject/' + type + '/channel' + mobil + '/' + first + '/' + second + '/' + cid + '.json';
    } else {
        var index = Math.floor(cid / 10000);
        url = '/live/videos/channel' + mobil + '/' + index + '/' + cid + '.json';
    }

    url = GetHttp() + host + url + '?time=' + (new Date()).getTime();
    $.ajax({
        url: url,
        type:'GET',
        dataType:'json',
        success:function(data){
            if (data.code == 0){
                //CloseLoading();
                var show_live = true;
                if (isPhone && data.platform && data.platform == 2 && (show_live || match.status == 0)) {//如果是PC端的线路，未开始比赛或者在直播中，则提示
                    $('#MyFrame').html('<p class="noframe">该比赛暂无手机信号，请使用<b>电脑浏览器</b> 打开<img class="code" src="/img/pc/image_qr_868.jpg">加微信 <b>kanqiu868</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>')
                    return;
                }
                if(!show_live){
                    // var match = data.match;//倒计时
                    // if (match.status && match.status == 0) {
                    //     countdownHtml(match.hour_html, match.minute_html, match.second_html);
                    // }
                    return;
                }else if(show_live){
                    if (data.ad && data.ad == 2) {
                        show_ad = false;
                    }
                    var Link = getLink(data);
                    var PlayType = data.player;

                    if (PlayType == 11) { //iframe
                        LoadIframe(Link)
                    }else if (PlayType == 12) { //ckplayer
                        CheckPlayerType(Link,1);
                    }else if (PlayType == 13) { //m3u8
                        LoadCK (Link)
                    }else if (PlayType == 14) { //flv
                        LoadFlv (Link);
                    }else if (PlayType == 15) { //rtmp
                        LoadRtmp (Link)
                    } else if (PlayType == 17) {
                        LoadClappr(Link);
                    } else if (PlayType == 18) {
                        LoadMP4(Link);
                    } else if (PlayType == 19) {//jsj
                        LoadIframe(window.jsj + Link);
                    }else if(PlayType == 100){//腾讯体育专用
                        $.ajax({
                            url: Link,
                            dataType: "jsonp",
                            success: function (data) {
                                if(data.playurl) {
                                    Link = data.playurl;
                                    if (isMobileWithJS()) {
                                        Link = Link.replace('.flv', '.m3u8');
                                        LoadCK(Link);
                                    }
                                    else {
                                        if (Link.indexOf('.flv') != -1) {
                                            LoadFlv(Link);
                                        }
                                        else{
                                            LoadCK(Link);
                                        }
                                    }
                                }
                                else{
                                    document.getElementById('MyFrame').innerHTML = '<p class="loading">暂无直播信号</p>';
                                }
                            }
                        });
                    }
                    else{
                        CheckPlayerType(Link,0)
                    }

                }
            }else{
                document.getElementById('MyFrame').innerHTML = '<p class="loading">暂无直播信号</p>';
            }
        }
    })
}

function LoadClappr(Link) { //clappr
    $.getScript("https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js",function(){  //加载test.js,成功后，并执行回调函数
        $.getScript("https://cdn.jsdelivr.net/clappr.level-selector/latest/level-selector.min.js",function(){
            var data = {
                source: Link,
                replace: true,
                keyUrl: 'http://m3u8.navixstream.com/navixstream.key'
            };
            player = new Clappr.Player({
                source: data.source,
                mimeType: 'application/x-mpegURL',
                autoPlay: false,
                height: '100%',
                width: '100%',
                watermark: '/watermark.png',
                position: 'top-right',
                mediacontrol: {seekbar: '#FF0000', buttons: '#FF0000'},
                parentId: '#MyFrame'
            });
        });
    });
    if (isPhone()) {
        $('video').attr('playsinline','true')
    }
}


//获取播放链接
function getLink (data) {
    if (data.type == 2 && data.js) {
        eval(data.js);
        return play_url;
    }else{
        return data.playurl;
    }
}

//按链接选择播放方式
function CheckPlayerType (Link,CK) {
    if(Link.indexOf('.mp4') != -1){
        LoadMP4(Link);
    }else if (Link.indexOf('.flv') != -1) {
        LoadFlv (Link);
    }else if (Link.indexOf('rtmp://') == 0) {
        LoadRtmp (Link);
    }else if (Link.indexOf('.m3u8') != -1) {
        LoadCK (Link);
    }else if (Link.indexOf('player.pptv.com') != -1) {
        LoadPPTV(Link)
    }else if (Link.indexOf('staticlive.douyucdn.cn') != -1 || Link.indexOf('upstatic.qiecdn.com') != -1 || Link.indexOf('liveshare.huya.com') != -1) {
        LoadTV(Link)
    }else if (Link.indexOf('http://') != -1) {
        LoadIframe(Link);
    }else if (CK == 0) {
        LoadCK(Link);
    }else{
        document.getElementById('MyFrame').innerHTML = '<p class="loading">暂无直播信号</p>';
    }
}

//判断手机还是PC
function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone","SymbianOS", "Windows Phone","iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}

//倒计时
function countDown() {
    var div = $("p.noframe");
    if (div.length > 0) {
        var is = div.find('i');
        var seconds = $("#second");
        if (seconds.length == 0) {
            return;
        }
        var second = parseInt($("#second").html());
        second = second - 1;
        if (second < 0) {
            second = 59;
            countDownMinute();
        }
        //倒计时 五分钟 内刷新页面
        var minute = parseInt($('#minute').html());
        if ($("#hour").length == 0 && (isNaN(minute) || (minute * 60 + second) <= (5 * 60)) ) {
            location.reload();
        }
        $("#second").html(second);
    }
}

function countDownMinute() {
    var minutes = $("#minute");
    var hours = $("#hour");
    if (minutes.length == 0) {
        return;
    }
    var minute = parseInt(minutes.html());
    minute = minute - 1;
    if (hours.length == 1) {
        if (minute < 0) {
            minute = 59;
            countDownHour();
        }
        minutes.html(minute);
    } else {
        if (minute < 1) {
            minutes.remove();
            var divHtml = $("#p.noframe").html();
            divHtml = divHtml.replace('分钟', '');
            $("#p.noframe").html(divHtml);
        } else {
            minutes.html(minute);
        }
    }
}

function countDownHour() {
    var hours = $("#hour");
    if (hours.length == 0) {
        return;
    }
    var hour = parseInt($("#hour").html());
    hour--;
    if (hour == 0) {
        hours.remove();
        var divHtml = $("p.noframe").html();
        divHtml = divHtml.replace('小时', '');
        $("p.noframe").html(divHtml);
    } else {
        hours.html(hour);
    }
}

function showCode() {
    if (isPhone()) {
        //如果是手机
        var clipboard = window.cdn_url + "/js/public/pc/clipboard.min.js";
        $.getScript(clipboard, function(){
            //处理样式
            var wx = document.getElementById("WxAddPhone");
            if (!wx) {
                $('body').addClass('bb');
                $('#MyFrame').css('height',$('#MyFrame').height() - 70 + 'px');
                $('#MyFrame video').css('height',$('#MyFrame').height() + 'px');
                $('body').append('<div id="WxAddPhone"><p>关注“ i 看球”<span id="id_copy" data-clipboard-text="i看球">[复制]</span>公众号，获取活动码</p><p><input type="text" name="code"><button onclick="validCode();">领红包</button></p></div>');
                var btn = document.getElementById('id_copy');
                var clipboard = new Clipboard(btn);
                clipboard.on('success', function(e) {
                    alert('复制成功！')
                });
                clipboard.on('error', function(e) {
                    // console.log(e);
                });
            }
        });

    }else{ //如果不是手机
        // var wxAdd = document.getElementById("WxAdd");
        // if (!wxAdd) {
        //     var imgUrl = window.cdn_url + "/img/pc/WechatIMG60.jpeg";
        //     $('body').append('<div id="WxAdd"><p>输入高清码，看高清视频</p><p class="input"><input type="text" name="code"><button class="com" onclick="validCode();">确认</button></p><p><button class="get">获取</button><span class="close">收起</span></p><p class="app"><img src="' + imgUrl + '">关注“爱看球”公众号<br/>获取高清信号码</p><p class="show">切换高清信号</p></div>');
        //
        //     $('#WxAdd p.show').click(function(){
        //         $('#WxAdd').removeClass('close');
        //     })
        //     $('#WxAdd button.get').click(function(){
        //         $('#WxAdd p.app').css('display','block');
        //     })
        //     $('#WxAdd span.close').click(function(){
        //         $('#WxAdd').addClass('close');
        //         $('#WxAdd p.app').removeAttr('style');
        //     })
        // }
    }
}

function closeCode() {
    if (isPhone() && $('#WxAddPhone').length > 0) {
        $('#MyFrame').css('height',$('#MyFrame').height() + 70 + 'px');
        $('#MyFrame video').css('height',$('#MyFrame').height() + 'px');
    }
    $('#WxAdd').remove();
    $('#WxAddPhone').remove();
    $('body').removeClass('bb');
}

function validCode() {
    var code;
    if (isPhone()) {
        code = $("#WxAddPhone input[name=code]").val();
    } else {
        code = $("#WxAdd input[name=code]").val();
    }
    if (code && $.trim(code).length > 0) {
        $.ajax({
            "url": "/live/valid/code?time=" + (new Date()).getTime(),
            "type": "post",
            "data": {"code": code},
            "success": function (json) {
                if (json) {
                    if (json.code == 200) {
                        maxTimeOut++;
                        playerLink();
                    } else {
                        alert(json.msg);
                    }
                }
            },
            "error": function () {
                alert("验证失败");
            }
        });
    } else {
        alert('请输入验证码');
    }
}

//活动插件
function activeValid() {
    var code;
    if (!isPhone()) {
        code = $("#CloseADCode input[name=CloseAD]").val();
    }
    if (code && $.trim(code).length > 0) {
        valid_code = code;
        $.ajax({
            "url": "/live/valid/code?time=" + (new Date()).getTime(),
            "type": "post",
            "data": {"code": code},
            "success": function (json) {
                if (json) {
                    if (json.code == 200) {
                        $('#CloseADCode').remove();
                        CKobject.getObjectById('ckplayer_a1').textBoxClose('AttWX');
                        var param = getParam();
                        if (param.type && param.type == 9) {
                            maxTimeOut++;
                            playerLink();
                        }
                    } else {
                        alert(json.msg);
                    }
                }
            },
            "error": function () {
                alert("验证失败");
            }
        });
    } else {
        alert('请输入验证码');
    }
}

function playerLink() {
    var param = getParam();
    var cid = param.cid;
    var type = param.type;
    if (cid && cid != '') {
        if (type == 'video' || type == 'specimen' || type == 'hv') {
            PlayVideoSubject(cid, type);
        } else {
            PlayVideoShare(cid, type);
        }
    }
}

function getParam() {
    var cid = GetQueryString('cid');
    var type = GetQueryString('type');
    if (cid && cid != '') {  } else {
        var str = window.location.pathname;
        var index = str .lastIndexOf("\/");
        str  = str .substring(index + 1, str .length);
        str = str.replace('.html','');
        var params = str.split("-");
        if (params.length == 3) {
            cid = params[1];
            type = params[2];
        }
    }
    return {'cid': cid, 'type': type};
}

function checkActive() {
    $.ajax({
        "url": "/json/dd_image/active.json?time=" + (new Date()).getTime(),
        "success": function (json) {
            if (json && json.txt && json.code && (json.txt != active_text || json.code != active_code) ) {
                active_text = json.txt;
                active_code = json.code;
                showWXCode(active_text, active_code);
            } else {
                var cookie_code = getCookie('LIVE_HD_CODE_KEY');
                if (show_ad && !firstShowCode && cookie_code != valid_code){
                    showWXCode(active_text, active_code);
                }
            }
            firstShowCode = true;
        },
        "error": function () {
            showWXCode(active_text, active_code);
        }
    });
}

//关注微信引导
function showWXCode (Text,Code) { //文字和二维码图片地址，文字可以使用\n换行，最多两行。
    if (Code == "") return;
    CKobject.getObjectById('ckplayer_a1').textBoxClose('AttWX');
    var Status = CKobject.getObjectById('ckplayer_a1').getStatus();
    var Coor = '0,2,130,-62';
    if (Text.split('\n').length > 1) {
        var len = Text.split('\n').length - 1;
        Coor = '0,2,130,' + (-62 - len * 20);
    }
    var WXCode = {
        name: 'AttWX', //该文本元件的名称，主要作用是关闭时需要用到
        coor: Coor, //坐标
        text: '{font color="#FFFFFF" face="Microsoft YaHei,微软雅黑" size="12"}' + Text + '{/font}', //文字
        bgColor: '0x000000', //背景颜色
        borderColor: '0x000000', //边框颜色
        radius: 3, //圆角弧度
        alpha:0,//总体透明度
        bgAlpha: 50, //背景透明度
        xWidth: 20, //宽度修正
        xHeight: 5, //高度修正
        pic: [Code,'/img/pc/icon_close_btn_video.png','temp/temp3.png'], //附加图片地址数组，可以增加多个图片
        pwh:[[120,120],[20,20],[1,1]],//图片缩放宽高，和上面图片一一对应
        pEvent:[['',''],['javascript','CloseWXCode()'],['close','']],//图片事件数组
        pCoor:  ['0,2,-120,-120','2,0,0,-20','2,2,-30,-30'], //图片坐标数组
        pRadius: [10,0,0] //附加图片的弧度
        // tween:[['x',1,50,0.3],['alpha',1,100,0.3]]//缓动效果
    }
    CKobject.getObjectById('ckplayer_a1').textBoxShow(WXCode);
    // setTimeout(function(){
    //     CKobject.getObjectById('ckplayer_a1').textBoxTween('AttWX',[['x',1,130,0.4]]);
    // },100);
}

function CloseWXCode () {
    var cookie_code = getCookie('LIVE_HD_CODE_KEY');
    if (cookie_code != valid_code) {//判断是否也已经输入验证码，或者验证码是否正确
        CKobject.getObjectById('ckplayer_a1').quitFullScreen();
        ShowADCode();
    }else{
        CKobject.getObjectById('ckplayer_a1').textBoxClose('AttWX');
    }
}

function ShowADCode () {
    var Code = $('<div id="CloseADCode"><div class="in"><p class="title">获取关闭广告权限</p><button class="close"></button>' +
        '<div class="input"><input type="text" name="CloseAD" placeholder="请输入免广告码"><button onclick="activeValid();">获取权限</button></div>' +
        '<img src="/img/pc/WechatIMG60.jpeg"><p class="text">关注“爱看球”公众号，获取免广告码！</p></div></div>');

    Code.find('button.close').click(function(){
        $('#CloseADCode').remove();
    });

    $('body').append(Code)
}

//修改控制栏文字
function ChangeText (Text) {
    // 关注{font color='#e3f42c'}【i看球】{/font}公众号，看球领现金红包！
    CKobject.getObjectById('ckplayer_a1').changeStyle('pr_live',"{font color='#FFFFFF' face='Microsoft YaHei,微软雅黑' size='14'}" + Text + "{/font}");
}
function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=")
        if (c_start!=-1)
        {
            c_start=c_start + c_name.length+1
            c_end=document.cookie.indexOf(";",c_start)
            if (c_end==-1) c_end=document.cookie.length
            return unescape(document.cookie.substring(c_start,c_end))
        }
    }
    return ""
}