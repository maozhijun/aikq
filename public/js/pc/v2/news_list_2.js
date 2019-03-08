
function setPage () {
	Refresh()
}



/*用户加载，刷新最新列表*/
function Refresh () {
	// LeagueKeyword

	$.ajax({
		url: PubHeader + LeagueKeyword + '.json',
		success: function(res){
        	console.log(res)

        	ResetRightMatch(res.matches,8)
        	ResetRightVideo(res.videos)
    	}
    });
}














































