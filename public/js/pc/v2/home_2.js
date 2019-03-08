
var SchedulePage = 0;

function setPage () {
	$('#Right_part .player_rank .rank_tab_box p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on')
			.siblings('.on').removeClass('on')
			.end().parents('.player_rank').find('table').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})


	$('.el_con .rank .tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on')
			.siblings('.on').removeClass('on')
			.end().parents('.rank').find('.match, table').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})

	if ($('#Schedule li').length <= 5) {
		$('#Schedule button').attr('disabled','disabled');
	}else{
		var MaxSchedulePage = Math.ceil($('#Schedule li').length / 5)

		$('#Schedule button.left_btn').click(function(){
			SchedulePage--
			$('ul').animate({scrollLeft:$('ul').width() * SchedulePage},1000);
			$('#Schedule button').removeAttr('disabled')

			if (SchedulePage == 0) {
				$(this).attr('disabled','disabled');
			}
		})
		$('#Schedule button.right_btn').click(function(){
			SchedulePage++
			$('ul').animate({scrollLeft:$('ul').width() * SchedulePage},1000);
			$('#Schedule button').removeAttr('disabled')

			if (SchedulePage == MaxSchedulePage - 1) {
				$(this).attr('disabled','disabled');
			}
		})
	}

	$('.el_con .rank .tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on')
			.siblings('.on').removeClass('on')
			.end().parents('.rank').find('.match, table').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})
}

















































