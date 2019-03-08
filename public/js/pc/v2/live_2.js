
function setPage () {
	$('#Data .column a').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			$('#Analysis, #Lineup, #Technology').css('display','none').filter('#' + $(this).attr('value')).css('display','');
		}
	})

	Refresh()
}

/*用户加载，刷新最新列表*/
function Refresh () {
	// LeagueKeyword

	$.ajax({
		url: PubHeader + LeagueKeyword + '.json',
		success: function(res){
        	ResetRightNews(res.articles)
        	ResetRightVideo(res.videos)
        	ResetRightMatch(res.matches)
    	}
    });

    $.ajax({
		url: PubHeader + 'all.json',
		success: function(res){
        	ResetLeftMatch(res.matches)
    	}
    });
}













































