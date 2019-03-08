
function setPage () {
	$('#Left_part .text_con img').height('auto')

	Refresh()
}

/*用户加载，刷新最新列表*/
function Refresh () {
	// LeagueKeyword

	$.ajax({
		url: PubHeader + LeagueKeyword + '.json',
		success: function(res){
        	ResetRightVideo(res.videos)
        	ResetRightMatch(res.matches)
        	ResetLeftNews(res.articles)
    	}
    });

  //   $.ajax({
		// url: PubHeader + 'all.json',
		// success: function(res){
  //       	ResetLeftNews(res.news)
  //   	}
  //   });
}













































