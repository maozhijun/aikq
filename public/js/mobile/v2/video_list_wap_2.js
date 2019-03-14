var PageHeight = document.documentElement.clientHeight || document.body.clientHeight;
var BodyHeight = $('body').height();
var CanNextPage = true;

function setPage() {
	$('#Navigation .column_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('.video_list_con').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');

			nowPageType = $(this).attr('forItem');
			$(window,'body','html').scrollTop(0);
		}
	})

	$('.star .video_tab_con a').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			loadStarCon($(this).attr('forItem'))
		}
	})
}

$(window).scroll(function(){
	// console.log($(this).scrollTop())

	if ($(this).scrollTop() >= 16) {
		$('.video_tab_con').addClass('fixed');
	}else{
		$('.video_tab_con').removeClass('fixed');
	}

	if ($(this).scrollTop() + PageHeight >= BodyHeight && CanNextPage) {
		LoadNextPage()
	}
})

function LoadNextPage () {
	window['now' + nowPageType + 'Page']++

	CanNextPage = false;



	setTimeout(function(){
		CanNextPage = true
	},2000)



	console.log('nextPageLoaded')
}

function loadStarCon (ID) {
	console.log(ID)
}



















