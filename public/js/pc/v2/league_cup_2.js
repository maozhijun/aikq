
function setPage () {
	$('.player_rank .rank_tab_box p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on')
			$('.player_rank table').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
		}
	})

	$('#Left_part .schedule_con .round_con p[forItem]').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('#Left_part .schedule_con .round_con span').removeClass('on');

			$('#Left_part .schedule_con table').css('display','none').filter('[round=' + $(this).attr('forItem') + ']').css('display','');

		}
	})

	$('#Left_part .schedule_con .round_con span[forItem]').click(function(){
		if (!$(this).hasClass('on')) {
			$('#Left_part .schedule_con .round_con p').removeClass('on');
			$(this).addClass('on').siblings('.on').removeClass('on').end().parent().addClass('on');

			$('#Left_part .schedule_con table').css('display','none').filter('[round=' + $(this).attr('forItem') + ']').css('display','');

		}
	})

	Refresh()
}

/*用户加载，刷新最新列表*/
function Refresh () {
	// LeagueKeyword

	$.ajax({
		url: PubHeader + LeagueKeyword + '.json',
		success: function(res){
        	console.log(res)

        	ResetRightNews(res.articles)
        	ResetRightVideo(res.videos)
    	}
    });
}














































