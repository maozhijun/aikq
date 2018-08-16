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

	// $('table.list .choose p').attr('onclick','chooseP()')
	$('table.list .choose li').attr('onclick','chooseLi(this)')

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
function Copy() {
    $('button.copy').click(function(){
        $('#' + $(this).attr('for'))[0].select();
        document.execCommand("Copy"); // 执行浏览器复制命令
        alert("已复制好，可贴粘。");
    });
}
//筛选
function chooseP () {
	$('table.list .choose ul').css('display', function(){
		return $(this).css('display') == 'none' ? '' : 'none';
	});
}
function chooseLi (obj) {
	if (!$(obj).hasClass('on')) {
		var Par = $(obj).parents('.choose');

		Par.find('li.on').removeClass('on');
		Par.find('p').html($(obj).html());
		$(obj).addClass('on');

		$('table.list .choose').html(Par.html())
	}
	// $('table.list .choose ul').css('display', 'none');

	var trs = $('tr[match=1]');
	var type = obj.getAttribute('value');
	for (var i = 0 ; i < trs.length ; i++){
		var tr = trs[i];
		if (type == 'all'){
			tr.style.display='';
		}
		else if (type == 'lottery'){
			if (tr.getAttribute('lottery') == 1){
				tr.style.display='';
			}
			else{
				tr.style.display='none';
			}
		}
		else if (type == 'first'){
			if (tr.getAttribute('first') == 1){
				tr.style.display='';
			}
			else{
				tr.style.display='none';
			}
		}
		else if (type == 'imp'){
			if (tr.getAttribute('imp') == 2){
				tr.style.display='';
			}
			else{
				tr.style.display='none';
			}
		}
	}
}