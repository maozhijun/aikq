var CKHead = '/js/public/pc/ckplayer/';
var maxTimeOut = 0;
var ad_time = 5;
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
    if (isPhone()) {
        //如果是手机，加载5秒广告
        $('body').append('<div id="PhoneAD"><img src="/img/pc/demo.jpg"><p class="time">广告剩余：<b>5</b> 秒</p></div>');
        var ADRun = setInterval(function(){
            var Val = parseInt($('#PhoneAD b').html());
            if (Val > 0) {
                $('#PhoneAD b').html(Val - 1);
            }else{
                clearInterval(ADRun);
                $('#PhoneAD').remove();
            }
        },1000)
    }
	var cid = GetQueryString('cid');
    var type = GetQueryString('type');
	if (cid && cid != '') {
		PlayVideoShare(cid, type);
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
        l:'/img/pc/demo.jpg',
        d:'/img/pc/demo.jpg',
        z:'/img/pc/demo.jpg',
        t: maxTimeOut > 0 ? 0 : ad_time,
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
        l:'/img/pc/demo.jpg',
        d:'/img/pc/demo.jpg',
        z:'/img/pc/demo.jpg',
        t:maxTimeOut > 0 ? 0 : ad_time,
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
        l:'/img/pc/demo.jpg',
        d:'/img/pc/demo.jpg',
        z:'/img/pc/demo.jpg',
        t:maxTimeOut > 0 ? 0 : ad_time,
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
        var type = GetQueryString('type');
		PlayVideoShare(CID, type);
	}
}

function errorHandler () {
	if (maxTimeOut > 5) {
		return;
	}
	maxTimeOut++;
	console.log('error，重新请求链接');
	var CID = GetQueryString('cid');
    var type = GetQueryString('type');
	PlayVideoShare(CID, type);
}



//获取是S还是非S
function GetHttp () {
	if (location.href.indexOf('https://') != -1) {
		return 'https://';
	}else{
		return 'http://';
	}
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
    if (type == 9) {
        url = GetHttp() + host + '/match/live/url/channel/hd/' + cid;
    } else {
        if (window.isMobile) {
            url = GetHttp() + host + '/match/live/url/channel/mobile/' + cid + '.json';
        } else {
            url = GetHttp() + host + '/match/live/url/channel/' + cid + '.json';
        }
    }

	$.ajax({
		url: url,
		type:'GET',
		dataType:'json',
		success:function(data){
			if (data.code == 0){
				//CloseLoading();
				var match = data.match;
				var show_live = match.show_live;
                if (window.isMobile && data.platform && data.platform == 2 && (show_live || match.status == 0)) {//如果是PC端的线路，未开始比赛或者在直播中，则提示
                    $('#MyFrame').html('<p class="noframe">该比赛暂无手机信号，请使用<b>电脑浏览器</b> 打开<img class="code" src="/img/pc/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>')
                    return;
                }
                if(!show_live){
                    if (match.status && match.status == 0) countdownHtml(match.hour_html, match.minute_html, match.second_html);
					return;
				}else if(show_live){
                    if (data.type == 9 && !data.hd) {
                        showCode();
                    } else if (data.hd) {
                        closeCode();
                    }
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
						} else if (PlayType == 17) {
                            LoadClappr(Link);
                        } else{
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
	if (Link.indexOf('.flv') != -1) {
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
                $('body').append('<div id="WxAddPhone"><p>关注“ i 看球”<span id="id_copy" data-clipboard-text="i看球">[复制]</span>公众号，获取兑换码</p><p><input type="text" name="code"><button onclick="validCode();">看高清</button></p></div>');
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
        var wxAdd = document.getElementById("WxAdd");
        if (!wxAdd) {
            var imgUrl = window.cdn_url + "/img/pc/WechatIMG60.jpeg";
            $('body').append('<div id="WxAdd"><p>输入高清码，看高清视频</p><p class="input"><input type="text" name="code"><button class="com" onclick="validCode();">确认</button></p><p><button class="get">获取</button><span class="close">收起</span></p><p class="app"><img src="' + imgUrl + '">关注“爱看球”公众号<br/>获取高清信号码</p><p class="show">切换高清信号</p></div>');

            $('#WxAdd p.show').click(function(){
                $('#WxAdd').removeClass('close');
            })
            $('#WxAdd button.get').click(function(){
                $('#WxAdd p.app').css('display','block');
            })
            $('#WxAdd span.close').click(function(){
                $('#WxAdd').addClass('close');
                $('#WxAdd p.app').removeAttr('style');
            })
        }
    }
}

function closeCode() {
    $('#WxAdd').remove();
    $('#WxAddPhone').remove();
    if (isPhone()) {
        $('#MyFrame').css('height',$('#MyFrame').height() + 70 + 'px');
        $('#MyFrame video').css('height',$('#MyFrame').height() + 'px');
    }
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
            "url": "/live/valid/code",
            "type": "post",
            "data": {"code": code},
            "success": function (json) {
                if (json) {
                    if (json.code == 200) {
                        var cid = GetQueryString('cid');
                        var type = GetQueryString('type');
                        if (cid && cid != '') {
                            PlayVideoShare(cid, type);
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