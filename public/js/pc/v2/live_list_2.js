
var DateArr = [];
var DateTop = 0;

function setPage () {
	$('.type_con').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.type_con').removeClass('on');
			if ($(this).attr('forItem') == 'all') {
				$('tr').css('display','')
			}else{
				$('tr').css('display','none').filter('[type=' + $(this).attr('forItem') + ']').css('display','');
			}
		}
	})

	$('.el_con').each(function(){
		DateArr = DateArr.concat($(this).offset().top)
	})
	DateTop = $('#Date').offset().top;
}

$(window).scroll(function(){
	var ScT = $(window).scrollTop();

	if (ScT > DateTop) {
		$('#Date').addClass('fix');
	}else{
		$('#Date').removeClass('fix');
	}

	for (var i = DateArr.length - 1; i >= 0; i--) {
		if (ScT >= DateArr[i]) {
			if (!$('.date_con:eq(' + i + ')').hasClass('on')) {
				$('.date_con').removeClass('on').eq(i).addClass('on');
			}
			break;
		}
	}
})















































