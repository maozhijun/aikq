
var PlayerPage = 0;

function setPage () {
	if ($('.player_con .item').length <= 6) {
		$('.player_con button').attr('disabled','disabled');
	}else{
		for (var i = 0; i < $('.player_con .item').length%6; i++) {
			$('.player_con .player_list').append('<p class="item"></p>');
		}

		var MaxPlayerPage = $('.player_con .item').length / 6;

		$('.player_list').scrollLeft(function(){
			PlayerPage = Math.floor($(this).find('.on').parents('.item').prevAll().length / 6);
			if (PlayerPage != 0) {
				$('.player_con button.left').removeAttr('disabled');
			}
			if (PlayerPage == MaxPlayerPage - 1) {
				$('.player_con button.right').attr('disabled','disabled');
			}

			return PlayerPage * $(this).width()
		})

		$('.player_con button.left').click(function(){
			PlayerPage--
			$('.player_list').animate({scrollLeft:$('.player_list').width() * PlayerPage},800);
			$('.player_con button').removeAttr('disabled')

			if (PlayerPage == 0) {
				$(this).attr('disabled','disabled');
			}
		})
		$('.player_con button.right').click(function(){
			PlayerPage++
			$('.player_list').animate({scrollLeft:$('.player_list').width() * PlayerPage},800);
			$('.player_con button').removeAttr('disabled')

			if (PlayerPage == MaxPlayerPage - 1) {
				$(this).attr('disabled','disabled');
			}
		})
	}





}

















































