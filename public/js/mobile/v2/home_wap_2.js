

function setPage() {
	$('#Navigation .tab_con .tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			if ($(this).attr('forItem') == 'all') {
				$('.match_list_con a').css('display','')
			}else{
				$('.match_list_con a').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
			}

			$(window,'body','html').scrollTop(0);
		}
	})

	$('#Navigation .tab_con .tab_item[forItem=' + (GetQueryString('type') ? GetQueryString('type') : 'all' ) + ']').trigger('click');

	$('#Navigation .date_con .date_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('.match_list_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
		}

		$(window,'body','html').scrollTop(0);
	})
}

























