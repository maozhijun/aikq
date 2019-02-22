
function setPage () {
	$('#Data .column a').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('#Analysis, #Lineup, #Technology').css('display','none').filter('#' + $(this).attr('value')).css('display','');
		}
	})
}















































