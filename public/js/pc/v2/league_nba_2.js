

function setPage () {
	NowDate = new Date(NowDate);

	$('#League_info dt').click(function(){
		$('#League_info dd').css('display',function(){
			return $(this).css('display') == 'none' ? '' : 'none'
		})
	})

	$('#Left_part .date button').click(function(){
		changeDate($(this).hasClass('left'));
	})

	$('.player_rank .rank_tab_box p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on')
			$('.player_rank table').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
		}
	})
}

function changeDate (obj) {
	if (obj) { //上三天
		NowDate.setTime(NowDate.getTime() - 3*24*60*60*1000);
	}else{ //后三天
		NowDate.setTime(NowDate.getTime() + 3*24*60*60*1000);
	}

	console.log(NowDate)
}















































