function SetVideo () {
	var Part = $('#Content .part');
	for (var i = 0; i < Part.length; i++) {
		if (Part[i].id) {
			if (localStorage.getItem('Video_' + Part[i].id)) {
				var Local = JSON.parse(localStorage.getItem('Video_' + Part[i].id));
				$('#Content .part:eq(' + i + ') .btnline button:eq(' + Local.btn + ')').trigger("click");
			}else{
				// console.log($('#Content .part:eq(' + i + ') .btnline'));
				$('#Content .part:eq(' + i + ') .btnline button:first').trigger("click");
			}
		}
	}
}

function ChangeChannel (Link,obj) {
	if (obj.className.indexOf('on') != -1) {
		return;
	}

	var MatchID = $(obj).parents('.part')[0].id;
	// var MatchID = 123;

	var BtnNum = 0;

	var Par = $('#' + MatchID + ' button.line');
	for (var i = 0; i < Par.length; i++) {
		if (obj == Par[i]) {
			Par[i].className = 'line on';
			BtnNum = i;
		}else{
			Par[i].className = 'line';
		}
	}

	var Target = {
		'id': MatchID,
		'btn': BtnNum
	}

	localStorage.setItem('Video_' + MatchID,JSON.stringify(Target));

	if (!document.getElementById(MatchID).getElementsByTagName('iframe')[0]) {
		var Iframe = document.createElement('div');
		Iframe.className = 'iframe';
		Iframe.innerHTML = '<button class="close" onclick="MiniVideo(' + MatchID + ')">返回多屏</button><iframe src="' + Link + '"></iframe>';

		var Line = document.createElement('div');
		Line.className = 'btnline';
		Line.onclick = function () {
			if (this.className == 'btnline') {
				this.className = 'btnline on';
			}else{
				this.className = 'btnline';
			}
		}
		Line.innerHTML = '<p>' + obj.innerHTML + '</p>';
		Line.innerHTML += $('#' + MatchID + ' .btnline')[0].innerHTML;

		Iframe.appendChild(Line)

		document.getElementById(MatchID).appendChild(Iframe);
	}else{
		$('#' + MatchID + ' .iframe .btnline p')[0].innerHTML = obj.innerHTML;

		document.getElementById(MatchID).getElementsByTagName('iframe')[0].src = Link;
	}
}

function CloseVideo (obj) {
	var Box = obj.parentNode.parentNode;
	Box.id = '';
	Box.innerHTML = '<div class="empty"><button class="add" onclick="OpenAdd(this)"></button></div>';
}

function OpenAdd (thisObj) { //这里需要添加动态加载直播中的视频列表
	currAddObj = thisObj;
	MultiScreen();
}

function MultiScreen () {
	if (document.getElementById('multiScreen').style.display == 'block') {
		document.getElementById('multiScreen').style.display = 'none';
	}else{
		loadLivingMatch(1);
		document.getElementById('multiScreen').style.display = 'block';
	}
}