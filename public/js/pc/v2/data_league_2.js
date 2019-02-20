
function setPage () {
	$('.tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			changeCon($(this).attr('forItem'));
		}
	})

	$('.league_player p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');

			$('.tab_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			changeCon($('.' + $(this).attr('forItem') + ' .on').attr('forItem'));
		}
	})
}

function changeCon (num) {
	$('.con_inner').css('display','none').eq(num).css('display','');
}















































