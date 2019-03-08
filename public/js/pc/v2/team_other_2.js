
function setPage () {
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
}













































