
// var CKHead = 'https://img.liaogou168.com/kqm/file/ckplayer/';
var CKHead = 'js/ckplayer/';
var maxTimeOut = 0;
var WXCodeRun = false;

//获取链点参数
function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    };
    var rs = new RegExp("([\?&])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
    if (rs) {
        return decodeURI(rs[3]);
    } else {
        return '';
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

//判断手机
function isPhone() {
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		return true;
	}else{
		return false;
	}
}

function LoadVideo () {
	if (isWeiXin() && top.location.href.indexOf('aikq.cc') != -1) {
		//在这里写如果是微信的时候的状态
		$('#MyFrame').html('<p class="noframe">请使用 <b>浏览器</b> 打开<img class="code" src="img/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>')
		return;
	}

	if (isPhone()) {
		//如果是手机，加载5秒广告
		$('body').append('<div id="PhoneAD"><img src="img/demo.jpg"><p class="time">广告剩余：<b>5</b> 秒</p></div>');
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

	var CID = GetQueryString('cid');
	// CID = 4270;
	if (CID && CID != '' && false) {
		// CloseLoading()
		PlayVideoShare(CID);

		//PC时添加心跳请求
		if (!isPhone() && !WXCodeRun) {
			WXCodeRun = setInterval(function(){//每5秒请求一次服务器查看有没有更新微信信息
				// console.log(1)
			},5000)
		}
	}else{
		// CloseLoading()
		var Link = 'https://www.aikq.cc/js/public/pc/ckplayer/m3u8.swf&a=http://202.107.186.134:5010/nn_live.m3u8?id=cctv5m&lv=1&c=0&p=1&l=&d=&z=&t=0&loaded=loadHandler';
		// var Link = 'rtmp://live.hkstv.hk.lxdns.com/live/hks';
		// var Link = 'http://flv1.cdn.dlfyb.com/live/s_2345125.flv';
		// var Link = 'http://edge.ivideo.sina.com.cn/155824221.mp4?KID=sina,viask&Expires=1523635200&ssig=fxXCmI1Q0p';
		// LoadMP4 (Link)
		// return;

		if (Link.indexOf('.mp4') != -1) {
			LoadMP4 (Link)
		}else if (Link.indexOf('sportstream365.com') != -1){
			var ID = 1118354;

			LoadSports365(ID)
		}else if (Link == 'ttzb') {
			clickZBTT(NowID,Sport);
		}else if (Link == 'wcj') {
			clickWCJ(NowID);
		}else if (Link.indexOf('live.stream.tvmcloud.com') != -1) {
			Loadbaitv(Link)
		}else if (Link.indexOf('.flv') != -1) {
		 	LoadFlv (Link)
		}else if (Link.indexOf('rtmp') != -1) {
			LoadRtmp (Link)
		}else if (Link.indexOf('.m3u8') != -1) {
			LoadCK (Link)
		}else if (Link.indexOf('player.pptv.com') != -1) {
			LoadPPTV(Link)
		}else if (Link.indexOf('staticlive.douyucdn.cn') != -1 || Link.indexOf('upstatic.qiecdn.com') != -1 || Link.indexOf('liveshare.huya.com') != -1) {
			LoadTV(Link)
		}else{
			LoadIframe(Link)
		}
	}
}

function LoadCK (Link){ //m3u8
	// LoadClappr (Link);
	// return;
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
		l: maxTimeOut > 0 ? '' : 'img/demo.jpg',
		d:'img/demo.jpg',
		z:'img/demo.jpg',
		t: maxTimeOut > 0 ? 0 : 5,
		loaded:'loadHandler'
	};
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
		l: maxTimeOut > 0 ? '' : 'img/demo.jpg',
		d:'img/demo.jpg',
		z:'img/demo.jpg',
		t: maxTimeOut > 0 ? 0 : 5,
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
        l: maxTimeOut > 0 ? '' : 'img/demo.jpg',
		d:'img/demo.jpg',
		z:'img/demo.jpg',
		t: maxTimeOut > 0 ? 0 : 5,
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

function LoadClappr (Link) { //clappr
	$.getScript("https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js",function(){  //加载test.js,成功后，并执行回调函数
		$.getScript("https://cdn.jsdelivr.net/clappr.level-selector/latest/level-selector.min.js",function(){
			var data = {
		        source: Link,
		        replace: false,
		        keyUrl: 'http://m3u8.navixstream.com/navixstream.key'
		    };
			player = new Clappr.Player({
	            source: data.source,
	            mimeType: 'application/x-mpegURL',
	            autoPlay: false,
	            height: '100%',
	            width: '100%',
	            startLevel: 1,
	            plugins: {'core': [LevelSelector],startLevel: 1},
	            levelSelectorConfig: {
				    labelCallback: function(playbackLevel, customLabel) {
				        return customLabel + playbackLevel.level.height+'p'; // High 720p
				    }
				},
	            mediacontrol: {seekbar: '#FF0000', buttons: '#FF0000'},
	            parentId: '#MyFrame'
	        });
	        // player.core.plugins['0'].currentLevel.id = 1;
		});	        	
	});

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
	var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
	var video=[''+Link+'->video/mp4'];
	CKobject.embed( CKHead + 'ckplayer.swf','MyFrame','ckplayer_a1','100%','100%',false,flashvars,video,params);

	if (isPhone()) {
		$('video').attr('playsinline','true')
	}
}

function ShareWarm (Text) {
	var P = document.createElement('p');
	P.id = 'ShareWarm';
	P.innerHTML = Text;
	document.body.appendChild(P)
}

function ckmarqueeadv(){return '免费看球用料狗TV：<a href="http://www.lg310.com" target="_blank">lg310.com</a> 进千人球迷群领红包 加微信<span>fs188fs</span>'}

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
	// var FullScreen = CKobject.getObjectById('ckplayer_a1').fullScreen();
	if (Status.controlBarShow) {
		CKobject.getObjectById('ckplayer_a1').textBoxTween('AttWX',[['y',1,-32,0.4]]);
	}else{
		CKobject.getObjectById('ckplayer_a1').textBoxTween('AttWX',[['y',1,32,0.4]]);
	}
}

function playHandler (){
	// console.log(CKobject)
	$('#WaitWarm').remove()
}

function bufferHandler (num) {
	if (num > 100 || num < 0) {
		console.log(num)
		var CID = GetQueryString('cid');
		PlayVideoShare(CID);
	}
}

function errorHandler () {
	// if (maxTimeOut > 5) {
	// 	return;
	// }
	// maxTimeOut++;
	// console.log('error，重新请求链接');
	// // alert(this);
	// var CID = GetQueryString('cid');
	// PlayVideoShare(CID);
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
	$.ajax({
		// url:GetHttp() + 'www.lg310.com/match/live/url/channel/' + CID,
		url: GetHttp() + 'dev.cms.liaogou168.com/match/live/url/channel/' + CID,
		type:'GET',
		dataType:'json',
		success:function(data){
			if (data.code == 0){
				CloseLoading();
				var match = data.match;
				var show_live = match.show_live;
				if(!show_live){
					return;
					if (match.status == 0) {
						var p = '<p class="loading noframe"><img src="/img/pc/icon_restTime.png">距离比赛还有' + match.hour_html + match.minute_html + match.second_html+'</p>';
						document.getElementById('MyFrame').innerHTML = p;
						setInterval(countDown, 1000);
					}else if (match.status == -1) {
						var p = '<p class="loading noframe"><img src="/img/pc/icon_matchOver.png">比赛已结束</p>';
                    	document.getElementById('MyFrame').innerHTML = p;	
					}
				}else if(show_live){
					if (data.type == 1) { //如果是365，直接播放，不使用链接
						var ID = data.id;
						LoadSports365(ID)
					}else{ //其他，获取播放地址和播放方式

						//如果是自己的源，并且是低清，增加关注公众号广告，否则去掉
						if (true) {
							showCode();
						}else{
							closeCode();
						}

						var Link = getLink(data);
						var PlayType = data.play;
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
						}else if (PlayType == 16) { //rtmp
							LoadClappr (Link)
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

//高清码
function showCode(type) {
	if (isPhone()) {
		//如果是手机
		$.getScript("js/clipboard.min.js",function(){
			//处理样式
		  	$('body').addClass('bb');
		  	$('#MyFrame').css('height',$('#MyFrame').height() - 70 + 'px');
		  	$('#MyFrame video').css('height',$('#MyFrame').height() + 'px');

		  	var Text = type == 1 ? '去广告' : '看高清';
			$('body').append('<div id="WxAddPhone"><p>关注“ i 看球”<span id="id_copy" data-clipboard-text="i看球">[复制]</span>公众号，获取兑换码</p><p><input type="text" name="code"><button>' + Text + '</button></p></div>');
			var btn = document.getElementById('id_copy');
		    var clipboard = new Clipboard(btn);

		    clipboard.on('success', function(e) {
		        alert('复制成功！')
		    });

		    clipboard.on('error', function(e) {
		        // console.log(e);
		    });
		});

	}else{ //如果不是手机
		if (type == 1) {
			$('body').append('<div id="WxAdd"><p>输入密码，去广告</p><p class="input"><input type="text" name="code"><button class="com">确认</button></p><p><button class="get">获取</button><span class="close">收起</span></p><p class="app"><img src="img/WechatIMG60.jpeg">关注“爱看球”公众号<br/>获取去广告密码</p><p class="show">不再看广告</p></div>');
		}else{
			$('body').append('<div id="WxAdd"><p>输入高清码，看高清视频</p><p class="input"><input type="text" name="code"><button class="com">确认</button></p><p><button class="get">获取</button><span class="close">收起</span></p><p class="app"><img src="img/WechatIMG60.jpeg">关注“爱看球”公众号<br/>获取高清信号码</p><p class="show">切换高清信号</p></div>');
		}

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
// showCode(1)

function closeCode() {
	$('#WxAdd').remove();
	$('#WxAddPhone').remove();
	$('body').removeClass('bb');
}

//活动插件
//关注微信引导
function showWXCode (Text,Code) { //文字和二维码图片地址，文字可以使用\n换行，最多两行。
	CKobject.getObjectById('ckplayer_a1').textBoxClose('AttWX');
	var Coor = '0,2,130,-62';
	if (Text.indexOf('\n') != -1) {
		Coor = '0,2,130,-82';
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
		pic: ['img/WechatIMG60.jpeg','img/icon_close_btn_video.png','temp/temp3.png'], //附加图片地址数组，可以增加多个图片
		pwh:[[120,120],[20,20],[1,1]],//图片缩放宽高，和上面图片一一对应
		pEvent:[['',''],['javascript','CloseWXCode()'],['close','']],//图片事件数组
		pCoor: ['0,2,-120,-120','2,0,0,-20','2,2,-30,-30'], //图片坐标数组
		pRadius: [10,0,0] //附加图片的弧度
		// tween:[['x',1,50,0.3],['alpha',1,100,0.3]]//缓动效果
	}
	CKobject.getObjectById('ckplayer_a1').textBoxShow(WXCode);

	// setTimeout(function(){
	// 	CKobject.getObjectById('ckplayer_a1').textBoxTween('AttWX',[['x',1,130,0.4]]);
	// },100)
}

function CloseWXCode () {
	if (true) {
		CKobject.getObjectById('ckplayer_a1').quitFullScreen();

		ShowADCode()
	}else{
		CKobject.getObjectById('ckplayer_a1').textBoxClose('AttWX');
	}
}

function ShowADCode () {
	var Code = $('<div id="CloseADCode"><div class="in"><p class="title">获取关闭广告权限</p><button class="close"></button>' +
				 '<div class="input"><input type="text" name="CloseAD" placeholder="请输入免广告码"><button>获取权限</button></div>' +
				 '<img src="img/WechatIMG60.jpeg"><p class="text">关注“爱看球”公众号，获取验证码！</p></div></div>');

	Code.find('button.close').click(function(){
		$('#CloseADCode').remove();
	})

	$('body').append(Code)
}

//修改控制栏文字
function ChangeText (Text) {
	// 关注{font color='#e3f42c'}【i看球】{/font}公众号，看球领现金红包！
	CKobject.getObjectById('ckplayer_a1').changeStyle('pr_live',"{font color='#FFFFFF' face='Microsoft YaHei,微软雅黑' size='14'}" + Text + "{/font}");
}


//弹幕
var videoWidth = 0;
var videoHeight = 0;
var TextNumber = 0;
(function () {
	videoWidth = $('#MyFrame').width();
	videoHeight = $('#MyFrame').height() / 2;
})()
function popText (text,name) {
	TextNumber++;
	var Name = 'AttWX' + TextNumber
	var Top = parseInt((Math.random() * (videoHeight / 5 - 24)) + videoHeight * (TextNumber % 5 / 5));

	var Coor = '0,0,' + videoWidth + ',' + Top;
	var WXCode = {
		name: Name, //该文本元件的名称，主要作用是关闭时需要用到
		coor: Coor, //坐标
		text: '{font color="#FFFFFF" face="Microsoft YaHei,微软雅黑" size="14"}' + text + '{/font}', //文字
		bgColor: '0x000000', //背景颜色
		borderColor: '0x000000', //边框颜色
		radius: 3, //圆角弧度
		alpha:100,//总体透明度
		bgAlpha: 50, //背景透明度
		xWidth: 20, //宽度修正
		xHeight: 5, //高度修正
		// pic: ['img/WechatIMG60.jpeg','img/icon_close_btn_video.png','temp/temp3.png'], //附加图片地址数组，可以增加多个图片
		// pwh:[[120,120],[20,20],[1,1]],//图片缩放宽高，和上面图片一一对应
		// pEvent:[['',''],['javascript','CloseWXCode()'],['close','']],//图片事件数组
		// pCoor: ['0,2,-120,-120','2,0,0,-20','2,2,-30,-30'], //图片坐标数组
		// pRadius: [10,0,0] //附加图片的弧度
		// tween:[['x',1,Left,10]]//缓动效果
	}
	CKobject.getObjectById('ckplayer_a1').textBoxShow(WXCode);
}

function textBoxShowHandler(b){
   var Left = -(b.width);

    setTimeout(function(){
		CKobject.getObjectById('ckplayer_a1').textBoxTween(b.name,[['x',1,Left,10]]);
	},100)

	setTimeout(function(){
		CKobject.getObjectById('ckplayer_a1').textBoxClose(b.name);
	},11000)
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
//var socket = io.connect('http://localhost:6001');
var socket = io.connect('https://ws.aikanqiu.com');
socket.on('connect', function (data) {
    var mid = GetQueryString('mid',window.location);
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
    console.log(data);
    popText(data['nickname'] + ':' + data['message']);
    // $('#contet').append('<br>' + data['time'] + ' ' + data['nickname'] + ":" + data['message']);
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
    var nickname = '匿名';
    if (GetQueryString('nickname',window.location) && GetQueryString('nickname',window.location).length > 0){
        nickname = GetQueryString('nickname',window.location)
    }
    var req = {
        'message':message,
        'time':time,
        'verification':in_string,
        'nickname':nickname
    };
    socket.emit('user_send_message', req);
}

















