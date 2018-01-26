function GetQueryString(str,href) {
    var Href;
    if (href != undefined && href != '') {
        Href = href;
    }else{
        Href = location.href;
    };
    var rs = new RegExp("([\?&])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(Href);
    if (rs) {
        return decodeURI(rs[3]);
    } else {
        return '';
    }
}

function setPage () {
 	var u = navigator.userAgent;
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if (isiOS) {
        document.getElementsByTagName('head')[0].innerHTML += '<style type="text/css">' + 
        '@media only screen and (min-device-width: 320px) and (min-device-height: 568px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait){ ' + 
        '#Video{position: sticky; position: -webkit-sticky; top: 88px; z-index: 2;}' +
        '.tabbox{position: sticky; position: -webkit-sticky; top: 596px; z-index: 2;}' +
        '}</style>';
    }

    $('#Video button').click(function(){
    	if (this.className != 'on') {
    		$('#Video button.on').removeAttr('class');
    		$(this).attr('class','on');
    		document.getElementById('MyIframe').src = $(this).attr('value');;
    	}
    })

    $('#Video button:first').trigger("click");

}












