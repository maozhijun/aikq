var CKHead = window.cdn_url + '/js/public/pc/ckplayer/';
//获取链点参数
function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    }
    var rs = new RegExp("([\?&#])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
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
    CKobject.getObjectById('ckplayer_a1')._V_.muted = false; //安卓有可能出现默认静音，这里修改一下静音选项
    $('#WaitWarm').remove()
}

function errorHandler () {
    var nowTime = (new Date()).getTime() / 1000;

    if (matchStatus == 0 || matchTime > nowTime) {
        countdownHtmlNew();
    }
    return;
}

//获取是S还是非S
function GetHttp () {
    if (location.href.indexOf('https://') != -1) {
        return 'https://';
    }else{
        return 'http://';
    }
}

//获取播放地址
function PlayVideoShare (cid, type){
    var url;
    if (IEVersion() == 9 || IEVersion() == 8){
        if (isPhone()) {
            url = window.jsonHost + '/json/m/match/live/url/channel/' + cid + '.js';
        } else {
            url = window.jsonHost + '/json/pc/match/live/url/channel/' + cid + '.js';
        }
        // }
        url = url + '?time=' + (new Date()).getTime();
        $.getScript(url,function(){
            var data = JSON.parse(a);
            _playerShareCallback(data);
        })
    }
    else{
        if (isPhone()) {
            url = window.jsonHost + '/json/m/match/live/url/channel/' + cid + '.json';
        } else {
            url = window.jsonHost + '/json/pc/match/live/url/channel/' + cid + '.json';
        }
        // }
        url = url + '?time=' + (new Date()).getTime();
        $.ajax({
            url: url,
            type:'GET',
            dataType:'json',
            success:function(data){
                _playerShareCallback(data);
            }
        })
    }
}

function IEVersion() {
    var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
    var isIE = userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1; //判断是否IE<11浏览器
    var isEdge = userAgent.indexOf("Edge") > -1 && !isIE; //判断是否IE的Edge浏览器
    var isIE11 = userAgent.indexOf('Trident') > -1 && userAgent.indexOf("rv:11.0") > -1;
    if(isIE) {
        var reIE = new RegExp("MSIE (\\d+\\.\\d+);");
        reIE.test(userAgent);
        var fIEVersion = parseFloat(RegExp["$1"]);
        if(fIEVersion == 7) {
            return 7;
        } else if(fIEVersion == 8) {
            return 8;
        } else if(fIEVersion == 9) {
            return 9;
        } else if(fIEVersion == 10) {
            return 10;
        } else {
            return 6;//IE版本<=7
        }
    } else if(isEdge) {
        return 'edge';//edge
    } else if(isIE11) {
        return 11; //IE11
    }else{
        return -1;//不是ie浏览器
    }
}

function _playerShareCallback(data) {
    if (data){
        if (data.type == 2) {
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
            } else if (PlayType == 13) { //m3u8
                LoadCK (Link)
            } else if (PlayType == 14) { //flv
                LoadFlv (Link);
            } else if (PlayType == 15) { //rtmp
                LoadRtmp (Link)
            } else if (PlayType == 18) {
                LoadMP4(Link);
            }
            else{
                CheckPlayerType(Link,0);
            }
        }
    }else{
        document.getElementById('MyFrame').innerHTML = '<p class="loading">播放失败</p>';
    }
}

function playVideo (cid){
    var isPhone = window.isMobile;
    var mobil = isPhone ? '/mobile' : '';
    var url = '/json/video/player/' + cid + '.json';
    url = GetHttp() + host + url + '?time=' + (new Date()).getTime();

    $.ajax({
        url: url,
        type:'GET',
        dataType:'json',
        success:function(data) {
            if (data.code == 0){
                var link = getLink(data);
                var PlayType = data.player;

                if (PlayType == 11) {
                    LoadIframe(link);
                } else {
                    var videoObject = {
                        container: '#MyFrame',//“#”代表容器的ID，“.”或“”代表容器的class
                        variable: 'player',//该属性必需设置，值等于下面的new chplayer()的对象
                        flashplayer: false,//如果强制使用flashplayer则设置成true
                        video: link//视频地址
                    };
                    var player = new ckplayer(videoObject);
                }
            }else{
                document.getElementById('MyFrame').innerHTML = '<p class="loading">暂无直播信号</p>';
            }
        }
    })
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

function playerLink() {
    var param = getParam();
    var cid = param.cid;
    playVideo(cid);
}

function getParam() {
    var cid = GetQueryString('id');
    return {'cid': cid};
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