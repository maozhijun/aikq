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

function setPage () {
 	var u = navigator.userAgent;
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if (isiOS) {
        document.getElementsByTagName('head')[0].innerHTML += '<style type="text/css">' + 
        '@media only screen and (min-device-width: 320px) and (min-device-height: 568px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait){ ' + 
        '#Video{position: sticky; position: -webkit-sticky; top: 88px; z-index: 2;}' +
        '.tabbox{position: sticky; position: -webkit-sticky; top: 596px; z-index: 2;}' +
        '}</style>';
    }

    $('#Video button').click(function(){
    	if (this.className != 'on') {
    		$('#Video button.on').removeAttr('class');
    		var $this = $(this);
            $this.attr('class','on');
    		var isHttps = $this.attr("https");
    		var value = $(this).attr('value');
    		if (value && document.getElementById('Frame')) {
    		    if (/^https?:\/\//.test(value)) {
                    document.getElementById('Frame').src = $(this).attr('value');
                } else {
                    //处理域名
                    var host = window.location.host;
                    if (/^m\.dlfyb\.com/.test(host)) {
                        host = host.replace(/^m\./,"mp.");
                    } else {
                        host = host.replace(/^m\./,"www.");
                    }
                    if (isHttps == 1) {
                        value = 'https://' + host + value;
                    } else {
                        value = '//' + host + value;
                    }
                    document.getElementById('Frame').src = value;
                }
            }
    	}
    });

    $('#Video button:first').trigger("click");
    // if (isWeiXin()) {
    //     $('body').html($('body').html() + '<div id="WX"></div>')
    // }
}
function showWXCode (text, code) {
    $('#Content p').html(text.replace(/\n/g, '<br/>'));
    $('#Content img')[0].src = code;
}
function videoActive() {
    $.ajax({
        "url": "/m/dd_image/active.json?time=" + (new Date()).getTime(),
        "success": function (json) {
            if (json && json.txt && json.code) {
                showWXCode(json.txt, json.code);
            }
        },
        "error": function () {
            showWXCode('扫二维码进入群', '/img/pc/image_qr_868.jpg');
        }
    });
}
var wxCodeRun = setInterval(function(){//每5秒请求一次服务器查看有没有更新 活动信息
    //videoActive();
}, 5000);











