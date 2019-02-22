
var DateTop = 0;

function setPage () {
	DateTop = $('#Date').offset().top;
	
	$('[name="date"]').datepicker({format:"yyyy-mm-dd",language: "zh-CN"});

	if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
    }).blur(function() {
        var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur();
    };

    $('#Date input').change(function(){
    	var Time = new Date($(this).val())
    	console.log(Time.getTime())
    })
}

function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}

$(window).scroll(function(){
	var ScT = $(window).scrollTop();

	if (ScT > DateTop) {
		$('#Date').addClass('fix');
	}else{
		$('#Date').removeClass('fix');
	}
})












































