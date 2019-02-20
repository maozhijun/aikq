
function setPage () {
	$('.tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on')
			.siblings('.on').removeClass('on')
			.end().parents('.con_in').find('table').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})





}

















































