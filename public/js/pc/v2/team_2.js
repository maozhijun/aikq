
function setPage () {
	$('#Right_part a.nba_part').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('a.on').removeClass('on')

			$(this).parent().siblings('table.integral').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})
}














































