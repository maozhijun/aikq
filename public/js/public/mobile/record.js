
function setPage () {
    $('.tabbox button').click(function(){
    	if (!$(this).hasClass('on')) {
            $('#MyIframe').attr("src", $(this).attr("value"));
    		$(this).addClass('on').siblings('.on').removeClass('on');
    		//$('#MyIframe').attr("src", $(this).val())
    	}
    })

    // $('#MyIframe').attr('src',$('.tabbox button.on').attr('value'));
}






