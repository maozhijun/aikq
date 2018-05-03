function setPage(){
	setTab();

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

function setTab () {
	$('#Content .left_part .tab button').click(function(){
		if (!$(this).hasClass('on')) {
			$('#Content .left_part .tab button.on').removeClass('on');
			$(this).addClass('on');

			$('#Live, #Video, #Collect').css('display','none');
			$('#' + $(this).val()).css('display','');
		}
	})

	$('#Rank .tab button').click(function(){
		if (!$(this).hasClass('on')) {
			$('#Rank .tab button.on').removeClass('on');
			$(this).addClass('on');

			$('#East, #West').css('display','none');
			$('#' + $(this).val()).css('display','');
		}
	})
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