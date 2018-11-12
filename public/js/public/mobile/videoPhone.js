function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    };
    var rs = new RegExp("([\?&#])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
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


    // setSelect ();
    $('#Navigation select').change(function(){
        setSelect (this);
    });
    $('#Unload select').change(function(){
        setSelect (this);
        $('#Navigation select').val($(this).val()).css('display','');
    });



    $('#Content .tabbox button').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).addClass('on').siblings('.on').removeClass('on');
            $('#Data, #Player, #Technology, #News, #Record').css('display','none');
            $('#' + $(this).attr('value')).css('display','');
        }
    })

    $('#Player .h_a button').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).addClass('on').siblings('.on').removeClass('on');
            $('#Player .host, #Player .away').css('display','none');
            $('#Player .' + $(this).attr('value')).css('display','');
        }
    })
}
function showWXCode (text, code) {
    $('#Content p').html(text.replace(/\n/g, '<br/>'));
    $('#Content img')[0].src = code;
}
function videoActive() {
    $.ajax({
        "url": window.jsonHost + "/json/m/dd_image/active.json?time=" + (new Date()).getTime(),
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

function setSelect (obj) {
    var Target = $(obj);
    var src = Target.val();
    if (src == "") {
        return;
    }
    var $op = Target.find("option:selected");
    var ex = $op.attr("ex");
    if (ex == 1) {
        location.href = $op.val();
    } else {
        $('#Unload').css('display','none');
        $('#MyIframe').attr('src', src);
        $('#Navigation .select').css('display','').text(Target.find("option:selected").text());
    }
}











