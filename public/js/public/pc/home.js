
// window.onscroll = function () {
//     var TableHead = document.getElementById('TableHead');
//     // var Different = (document.documentElement.scrollTop || document.body.scrollTop) - (TableHead.parentNode.offsetTop + document.getElementById('Content').offsetTop)
//     if ((document.documentElement.scrollTop || document.body.scrollTop) - Math.abs(TableHead.parentNode.offsetTop + document.getElementById('Content').offsetTop) > 0) {
//         TableHead.style.display = 'table-header-group';
//     }else{
//         TableHead.style.display = 'none';
//     }
// }

function setBtnChecked () {
	var Btn = $('#multiScreen button.check');

	function CheckBtn (obj) {
		if (obj.className.indexOf('on') != -1) {
			obj.className = 'check';
		}else{
			if ($('#multiScreen button.check.on').length < 4) {
				obj.className = 'check on';
			}
		}
	}

	Btn.click(function(){
		CheckBtn(this);
	})
}

function MultiScreen () {
	if (document.getElementById('multiScreen').style.display == 'block') {
		document.getElementById('multiScreen').style.display = 'none';
	}else{
		document.getElementById('multiScreen').style.display = 'block';
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














