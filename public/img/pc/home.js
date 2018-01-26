var TopArr = []

window.onscroll = function () {
    var TableHead = $('#TableHead');
    // var Different = (document.documentElement.scrollTop || document.body.scrollTop) - (TableHead.parentNode.offsetTop + document.getElementById('Content').offsetTop)
    if ((document.documentElement.scrollTop || document.body.scrollTop) > 20) {
        TableHead.css('display','table');
    }else{
        TableHead.css('display','none');
    }

    for (var i = 0; i < TopArr.length; i++) {
    	if ((document.documentElement.scrollTop || document.body.scrollTop) > TopArr[i] && ((document.documentElement.scrollTop || document.body.scrollTop) < TopArr[i + 1]) || !TopArr[i + 1]) {
    		$('#TableHead tbody th').html($('#Show tbody th:eq(' + i + ')').html())
    		break;
    	}
    }
}

function setPage(){
	$('#TableHead').width($('#Show').width());
	$('#TableHead').css('margin-left','-' + ($('#Show').width()/2) + 'px')

	var BodyTh = $('#Show tbody th');
	$('#TableHead tbody th').html($('#Show tbody th:first').html())
	for (var i = 0; i < BodyTh.length; i++) {
		TopArr = TopArr.concat($('#Show tbody th:eq(' + i + ')').parent().offset().top - 100);
	}
}

function ChangeMoreTV () {
	if (document.getElementById('MoreTV').style.display == 'block') {
		document.getElementById('MoreTV').style.display = 'none';
	}else{
		document.getElementById('MoreTV').style.display = 'block';
	}
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














