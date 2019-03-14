
function setPage() {
	$('#Info_con .tab_item').click(function(){
		var that = $(this)
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('.rank_con, .match_con, .player_con, .news_list_con, .video_list_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			$(window,'body','html').scrollTop(0);
		}
	})

	var nowType = GetQueryString('type')
	if (nowType && nowType != '') {
		$('#Info_con .tab_item[forItem=' + nowType + ']').trigger('click');
	}
}









































































