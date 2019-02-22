
function setPage () {
	$('.player_rank .rank_tab_box p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on')
			$('.player_rank table').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
		}
	})
}

















































