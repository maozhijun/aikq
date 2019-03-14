

function setPage() {
	$('#Match_info .tab_con .tab_item').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on');
			
			$('#Data, #News, #Video').css('display','none').filter('#' + $(this).attr('forItem')).css('display','');

			$(window,'body','html').scrollTop(0);
		}
	})

	$('#Match_info select').change(function(){
        setSelect (this);
    });
}



function setSelect (obj) {
	var Target = $(obj);
    var src = Target.val();
    if (src == "") {
        return;
    }
    location.href = src;
}


$(window).scroll(function(){
	// console.log($(this).scrollTop())
	if (checkToBottom()) {
		console.log('Bottom');
	}
})


















