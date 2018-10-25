function setPage(){
    if (canSaveIE()) {
        $('#Navigation .inner').append('<a class="save" href="javascript:void(0)">【收藏本站】</a>');
        $('#Navigation a.save').click(function(e) {
            var href = 'https://www.aikanqiu.com', title = '爱看球，世界各大体育赛事直播！';
            try{
                if(window.sidebar){
                    sidebar.addPanel(title, href, "");
                }else{
                    external.addFavorite(href, title);
                }
            }catch(e){
                alert("加入收藏失败，请按Ctrl+D进行添加");
            }
            return false;
        });
    }

    LoadVideo();

    $('#Data .column a').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).siblings().removeClass('on').end().addClass('on');
            $('#Analysis, #Lineup, #Technology').css('display','none');
            $('#' + $(this).attr('value')).css('display','');
        }
    })
}

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

function LoadVideo () {
	var MatchID = location.href.split('/')[location.href.split('/').length -1].split('.html')[0];
	var NowBtn = GetQueryString('btn');
	var cid = GetQueryString('cid');
    var btn = {};
	if (cid) {
        $("#Info .line").find("#" + cid);
	}
	if (btn.length > 0) {
        btn.trigger("click");
	} else if (NowBtn && NowBtn != '' && $('#Info .line button:eq(' + parseInt(NowBtn) + ')')){
		$('#Info .line button:eq(' + parseInt(NowBtn) + ')').trigger("click");
	} else {
		$("#Info .line button:first").trigger("click");
	}
}
function ChangeChannel (Link,obj) {
	if (obj.className.indexOf('on') != -1) {
        return;
    }
	var MatchID = location.href.split('/')[location.href.split('/').length -1].split('.html')[0];
	var Btn = $('#Info .line button');
	for (var i = 0; i < Btn.length; i++) {
		if (obj == Btn[i]) {
			obj.className = 'on';
			BtnNum = i;
		}else{
			Btn[i].className = '';
		}
	}

	var Target = {
		'id': MatchID,
		'btn': BtnNum
	}

    // if(localStorage) {
    //     localStorage.setItem('Video_' + MatchID, JSON.stringify(Target));
    // }

	if (!document.getElementById('Frame')) {
		var Iframe = document.createElement('iframe');
		Iframe.id = 'Frame';
		Iframe.src = Link;
		Iframe.setAttribute('allowfullscreen','true');
		Iframe.setAttribute('scrolling','no');
		Iframe.setAttribute('frameborder','0');
		Iframe.width = '100%';
		Iframe.height = '100%';
		document.getElementById('Video').appendChild(Iframe);
	}else{
		document.getElementById('Frame').src = Link;
	}

	//document.getElementById('Share').getElementsByTagName('input')[0].value = Link;

}

function CloseLoading () {
	if (document.getElementById('Video').getElementsByTagName('p')[0]) {
		document.getElementById('Video').removeChild(document.getElementById('Video').getElementsByTagName('p')[0])
	}
}


function Copy() {
	var Url2 = document.getElementById('Share').getElementsByTagName('input')[0];
	Url2.select(); // 选择对象
	document.execCommand('Copy'); // 执行浏览器复制命令
	document.getElementById('Share').getElementsByTagName('span')[0].innerHTML = '复制成功！';
}

function ShowGGWarm (){
	if ($('.ADWarm_RU')) {
		$('.ADWarm_RU').css('display','block');
	}
}

function setADClose () {
	$('.adflag button.close,.adbanner button.close').on( "click", function(event){
		event.preventDefault(); // 阻止浏览器默认事件，重要 
		if ($(this).parents('a').parent().children('a').length == 1) {
			$(this).parents('.adflag,.adbanner').remove();
		}else{
			$(this).parents('a').remove();
		}
    })
	
}

function canSaveIE () {
    var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
    var isIE = userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1; //判断是否IE<11浏览器
    var isEdge = userAgent.indexOf("Edge") > -1 && !isIE; //判断是否IE的Edge浏览器
    var isIE11 = userAgent.indexOf('Trident') > -1 && userAgent.indexOf("rv:11.0") > -1;
    if(isIE || isIE11) {
        return true;
    }else{
        return false;
    }
}