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
			$(window,'body','html').scrollTop(0);
		}
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

			loadDateRecord($(this).val())

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

function loadDateRecord (Time) {
	var TargetDate = setMyTime(Time,'year');
	$('.match_list_con.other').html('');


	$.ajax({
		url: '//api.aikanqiu.com/api/recordData.json?date=' + TargetDate,
		dataType: 'jsonp',
		success: function(res){
			// console.log(res[TargetDate])

			for (var i = 0; i < res[TargetDate].records.length; i++) {
				var Target = res[TargetDate].records[i],
					NewRecord = $('<a href="' + Target.url + '" class="' + (Target.sport == '1' ? 'football' : (Target.sport == '2' ? 'basketball' : '')) + '">' +
						'<div class="team_con"><p class="' + (Target.hscore < Target.ascore ? 'lose' : '') + '"><span>' + Target.hscore + '</span>' + Target.hname + '</p>' +
						'<p class="' + (Target.ascore < Target.hscore ? 'lose' : '') + '"><span>' + Target.ascore + '</span>' + Target.aname + '</p></div>' +
						'<div class="info_con"><p>' + Target.lname + '</p><p>' + Target.time.split(' ')[1].split(':')[0] + ':' + Target.time.split(' ')[1].split(':')[1] + '</p></div><div class="status_con"></div></a>');

				$('.match_list_con.other').append(NewRecord)
			}

			$('#Navigation .column_con .column_item.on').removeClass('on').trigger('click');
		}
	});
}
