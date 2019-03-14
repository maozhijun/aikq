var PageHeight = document.documentElement.clientHeight || document.body.clientHeight;
var BodyHeight = $('body').height();

function setPage() {
	$('.data_list_con .run_line span').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');

			$(this).parents('.data_list_con').children(':not(.data_tab_con)').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			$(window,'body','html').scrollTop($(window,'body','html').scrollTop() < 16 ? $(window,'body','html').scrollTop() : 16);
		}
	})

	$('#Navigation .run_line .column_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');

			$('.data_list_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			$(window,'body','html').scrollTop(0);
		}
	})
}

$(window).scroll(function(){
	// console.log($(this).scrollTop())

	if ($(this).scrollTop() >= 16) {
		$('.data_tab_con').addClass('fixed');
	}else{
		$('.data_tab_con').removeClass('fixed');
	}
})



















