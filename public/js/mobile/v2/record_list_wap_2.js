var PageHeight = document.documentElement.clientHeight || document.body.clientHeight;
var BodyHeight = $('body').height();
var ChooseDate = 0;

function setPage() {
	$('#Navigation .column_con .column_item').click(function(){
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

	$('.date_con .date_item:not(.other)').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('.match_list_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
		}

		$(window,'body','html').scrollTop(0);
	})

	$('.date_con .date_item.other').click(function(){
		$(this).find('input').focus();
		$('.date_con .date_item').removeClass('on').filter('.other').addClass('on');
		$('.match_list_con').css('display','none').filter('.other').css('display','');
	}).find('input').blur(function(){
		if ($(this).val() != ChooseDate) {
			ChooseDate = $(this).val()

			$(this).siblings('.date').text($(this).val().split('-')[1] + '-' + $(this).val().split('-')[2])

			$(window,'body','html').scrollTop(0);
			
			alert(1)
		}
	})
}

$(window).scroll(function(){
	// console.log($(this).scrollTop())
	if ($(this).scrollTop() >= 16) {
		$('.date_con').addClass('fixed');
	}else{
		$('.date_con').removeClass('fixed');
	}
})


















