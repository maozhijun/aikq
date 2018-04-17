var TopArr = []

window.onscroll = function () {
    var TableHead = $('#TableHead');
    // var Different = (document.documentElement.scrollTop || document.body.scrollTop) - (TableHead.parentNode.offsetTop + document.getElementById('Content').offsetTop)
    if ((document.documentElement.scrollTop || document.body.scrollTop) > 20) {
        TableHead.css('display','table');
    }else{
        TableHead.css('display','none');
    }

    for (var i = 0; i < TopArr.length; i++) {
    	if ((document.documentElement.scrollTop || document.body.scrollTop) > TopArr[i] && ((!TopArr[i + 1] || (document.documentElement.scrollTop || document.body.scrollTop) < TopArr[i + 1]))) {
    		$('#TableHead tbody th').html($('#Show tbody th:eq(' + i + ')').html())
    		break;
    	}
    }
}

function setPage(){
	$('#TableHead').width($('#Show').width());
	// $('#TableHead').css('marginLeft',$('#Show').width()/-2);
	$('#TableHead').css('left',$('#Content').offset().left);

	var BodyTh = $('#Show tbody th');
	$('#TableHead tbody th').html($('#Show tbody th:first').html())
	for (var i = 0; i < BodyTh.length; i++) {
		TopArr = TopArr.concat($('#Show tbody th:eq(' + i + ')').parent().offset().top - 100);
	}

	if (canSaveIE()) {
		$('#Navigation .inner').append('<a class="save" href="javascript:void(0)">【收藏本站】</a>');
		$('#Navigation a.save').click(function(e) {
			var href = 'http://www.aikq.cc', title = '爱看球，世界各大体育赛事直播！';
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
}

function ChangeMoreTV () {
	if (document.getElementById('MoreTV').style.display == 'block') {
		document.getElementById('MoreTV').style.display = 'none';
	}else{
		document.getElementById('MoreTV').style.display = 'block';
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


$(window).resize(function() {
	$('#TableHead').width($('#Show').width());
	$('#TableHead').css('left',$('#Content').offset().left);
});










