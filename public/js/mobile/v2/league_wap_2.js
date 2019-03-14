
function setPage() {
	$('#Info_con .tab_item').click(function(){
		var that = $(this)
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('.knockout, .rank, .match, .player, .news, .video').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			$(window,'body','html').scrollTop(function(){
				var SCT = 0;
				if (that.attr('forItem') == 'match') { //赛程切换
					SCT = $('.match_con .match_list_date.on').offset().top - 154
				}
				return SCT
			});
		}
	})

	var nowType = GetQueryString('type')
	if (nowType && nowType != '') {
		$('#Info_con .tab_item[forItem=' + nowType + ']').trigger('click');
	}



	$('.player_con .col_con p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('.player_con .player_change_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			$('.inner_con').scrollTop(0);
		}
	})

	$('#Info_con select').change(function(){
        setSelect (this);
    });
}



function setSelect (obj) {
	var Target = $(obj);
    var src = Target.val();
    if (src == "") {
        return;
    }
    location.href = src;
}


$(window).scroll(function(){
	// console.log($(this).scrollTop())
	if (checkToBottom()) {
		console.log('Bottom');
	}
})





































































