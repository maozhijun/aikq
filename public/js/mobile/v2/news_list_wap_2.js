var CanNextPage = true;

function setPage() {
	var LeftVal = $('#Navigation .column_item.on').offset().left;

	if (LeftVal > $('body').width() * 0.7) {
		$('#Navigation .column_con .run_line').animate({scrollLeft: 300},500)
	}
}

$(window).scroll(function(){
	// console.log($(this).scrollTop())
	if (checkToBottom()) {
		LoadNextPage()
	}
})

function LoadNextPage () {
	nowPage++

	CanNextPage = false;



	setTimeout(function(){
		CanNextPage = true
	},2000)



	console.log('nextPageLoaded')
}





















