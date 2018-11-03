//屏幕比例调整
function changeWindow () {
    var win_W = window.screen.width;
    var win_Dpr = 2; //不实用window.devicePixelRatio，因为有很多奇葩分辨率手机;
    if (win_Dpr * win_W >= 2000) {
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no');
    }else if (win_Dpr * win_W >= 1200) {
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75, user-scalable=no');
    }else if (win_Dpr * win_W < 650) {
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=0.45, maximum-scale=0.45, minimum-scale=0.45, user-scalable=no');
    }else{
        document.querySelector('meta[name="viewport"]').setAttribute('content','width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no');
    }
}

changeWindow()

window.addEventListener('resize', function() {
    changeWindow()
}, false);

//获取链点参数
function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    };
    var rs = new RegExp("([\?&#])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
    if (rs) {
        return decodeURI(rs[3]);
    } else {
        return '';
    }
}

//获取滚动距离
function getPageScroll() {
  var xScroll, yScroll;
  if (self.pageYOffset) {
    yScroll = self.pageYOffset;
    xScroll = self.pageXOffset;
  } else if (document.documentElement && document.documentElement.scrollTop) { // Explorer 6 Strict
    yScroll = document.documentElement.scrollTop;
    xScroll = document.documentElement.scrollLeft;
  } else if (document.body) {// all other Explorers
    yScroll = document.body.scrollTop;
    xScroll = document.body.scrollLeft;  
  }
  arrayPageScroll = new Array(xScroll,yScroll);
  return arrayPageScroll;
};

//状态弹框
function Alert (type,text) { //type:loading\success\error，text为提示内容
    var AlertBox;
    if (document.getElementById('Alert') == undefined) {
        AlertBox = document.createElement('div');
        AlertBox.id = 'Alert';
        AlertBox.setAttribute('status','loading');
        AlertBox.innerHTML = '<div class="loading">-</div><div class="success">-</div><div class="error">-</div>';
        document.body.appendChild(AlertBox);
    }else{
        AlertBox = document.getElementById('Alert');
    };
    AlertBox.setAttribute('status',type);
    AlertBox.getElementsByClassName(type)[0].innerHTML = text;
    if (type != 'loading') {
        setTimeout('document.body.removeChild(document.getElementById("Alert"))',1500);
    };
}

function closeLoading () {
    document.body.removeChild(document.getElementById('Alert'));
}

//确认弹框
function ComfirmAlert (content,Event,Title,canText,comText) { //content为提示文案，Event为确认事件
    if (document.getElementById('ComfirmBox') == undefined) {
        if (canText == undefined) {
            canText = '取消';
        };
        if (comText == undefined) {
            comText = '确认';
        };
        var Box = document.createElement('div');
        Box.id = 'ComfirmBox';
        Box.innerHTML = '<div class="default"><div class="title">' + Title + '</div>' +
                        '<div class="comText">' + content + '</div>' +
                        '<div class="btn"><button onclick="ComfirmAlert()">' + canText + '</button><button class="comfirm">' + comText + '</button></div>';

        if (Event != '') {
            Box.getElementsByTagName('button')[1].setAttribute('onclick',Event);
        }else{
            Box.getElementsByClassName('btn')[0].removeChild(Box.getElementsByTagName('button')[1]);
        }

        document.body.appendChild(Box);
    }else{
        document.body.removeChild(document.getElementById('ComfirmBox'));
    }
}

