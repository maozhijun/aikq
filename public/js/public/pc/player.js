var CKHead = '/js/public/pc/ckplayer/';
var maxTimeOut = 0;
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

function LoadVideo () {
	var CID = GetQueryString('cid');
	if (CID && CID != '') {
		PlayVideoShare(CID);
	}
}

function LoadCK (Link){ //m3u8
	if ((Link.indexOf('http://') == 0 || Link.indexOf('https://') == 0) && IsPC()) {
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
	};
	var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
	var video=[''+Link+'->video/mp4'];
	CKobject.embed( CKHead + 'ckplayer.swf','MyFrame','ckplayer_a1','100%','100%',false,flashvars,video,params);
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
	};
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
        // CKobject.getObjectById('ckplayer_a1').addListener('play',playHandler);
        // CKobject.getObjectById('ckplayer_a1').addListener('buffer',bufferHandler);
        CKobject.getObjectById('ckplayer_a1').addListener('error',errorHandler);
    }
    else{
        console.log('播放器已加载，调用的是Flash播放模块');
        // CKobject.getObjectById('ckplayer_a1').addListener('play','playHandler');
        // CKobject.getObjectById('ckplayer_a1').addListener('buffer','bufferHandler');
        CKobject.getObjectById('ckplayer_a1').addListener('error','errorHandler');
    }
}

function playHandler (){
	console.log(CKobject)
}

function bufferHandler (num) {
	if (num > 100 || num < 0) {
		console.log(num)
		var CID = GetQueryString('cid');
		PlayVideoShare(CID);
	}
}

function errorHandler () {
	if (maxTimeOut > 10) {
		return;
	}
	maxTimeOut++;
	console.log('error，重新请求链接');
	var CID = GetQueryString('cid');
	PlayVideoShare(CID);
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
function PlayVideoShare (CID){
	var url = GetHttp() + host + '/match/live/url/channel/' + CID + '.json';
	$.ajax({
		url: url,
		type:'GET',
		dataType:'json',
		success:function(data){
			if (data.code == 0){
				CloseLoading();
				var match = data.match;
				var show_live = match.show_live;
				if(!show_live){
					return;
				}else if(show_live){
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
							LoadFlv (Link)
						}else if (PlayType == 15) { //rtmp
							LoadRtmp (Link)
						}else{
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
	if(Link.indexOf('zb.tc.qq.com') != -1){
        GoTcPlayer(Link);
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
	}else if (CK == 0) {
		LoadIframe(Link);
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














