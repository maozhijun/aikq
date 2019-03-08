
var DateTop = 0;

function setPage () {
	$('.date_con').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			if ($(this).attr('forItem') == 'all') {
				$('tr').css('display','')
			}else{
				$('tr').css('display','none').filter('[type$=' + $(this).attr('forItem') + ']').css('display','');
			}
		}
	})

	DateTop = $('#Date').offset().top;

	$('[name="date"]').datepicker({format:"yyyy-mm-dd",language: "zh-CN"}).change(function(){
		console.log($(this).val())
	});

	$(GetQueryString('type') ? '.date_con[forItem=' + GetQueryString('type') + ']' : '.date_con:first').trigger('click');
}

$(window).scroll(function(){
	var ScT = $(window).scrollTop();

	if (ScT > DateTop) {
		$('#Date').addClass('fix');
	}else{
		$('#Date').removeClass('fix');
	}
})















































