
function setPage(){
	$('#Hot button.left').click(function () {
		scrollLeft()
	})

	$('#Hot button.right').click(function () {
		scrollRight()
	})
}

function setADClose () {
	$('.adflag button.close,.adbanner button.close').on( "click", function(event){
		event.preventDefault(); // 阻止浏览器默认事件，重要 
		if ($(this).parents('a').parent().children('a').length == 1) {
			$(this).parents('.adflag,.adbanner').remove();
		}else{
			$(this).parents('a').remove();
		}
    })
	
}

function scrollLeft () {
	var List = $('#Hot .list');
	var Item = $('#Hot .list .item');
	List.animate({
		scrollLeft: $(this).width()
	}, 1000, function () {
		if (Item.width() * Item.length - List.width() - List.scrollLeft() < 10) {
			$('#Hot button.left').attr('disabled',true);
		}
	});


	$('#Hot button.right').removeAttr('disabled');
}

function scrollRight () {
	var List = $('#Hot .list');
	var Item = $('#Hot .list .item');
	List.animate({
		scrollLeft: List.scrollLeft() - $(this).width()
	}, 1000, function () {
		var List = $('#Hot .list');

		if (List.scrollLeft() < 10) {
			$('#Hot button.right').attr('disabled',true);
		}

	});

	$('#Hot button.left').removeAttr("disabled");
}





