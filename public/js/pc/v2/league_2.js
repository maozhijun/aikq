
function setPage () {
	$('.data_con .tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on')
			.siblings('.on').removeClass('on')
			.end().parents('.con_in').find('table').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})

	$('#Left_part .schedule_con .round_con p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
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












































