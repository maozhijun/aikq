
function setPage () {
 	var u = navigator.userAgent;
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if (isiOS) {
        document.getElementsByTagName('head')[0].innerHTML += '<style type="text/css">' + 
        '@media only screen and (min-device-width: 320px) and (min-device-height: 568px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait){ ' + 
        '.tabbox{position: sticky; position: -webkit-sticky; z-index: 2;}' +
        '}</style>';
    }

    $('#Content .tabbox button').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).addClass('on').siblings('.on').removeClass('on');
            $('#Data, #Player, #Technology, #News, #Record').css('display','none');
            $('#' + $(this).attr('value')).css('display','');
        }
    })

    $('#Player .h_a button').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).addClass('on').siblings('.on').removeClass('on');
            $('#Player .host, #Player .away').css('display','none');
            $('#Player .' + $(this).attr('value')).css('display','');
        }
    })

}






