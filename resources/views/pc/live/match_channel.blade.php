<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>爱看球</title>
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/css/pc/player.css?time=20180203">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{$cdn}}/img/pc/ico.ico" href="{{$cdn}}/img/pc/ico.ico">
</head>
<body scroll="no">
<iframe width="100%" height="100%" id="MyFrame">
</iframe>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
    //获取是S还是非S
    function GetHttp () {
        if (location.href.indexOf('https://') != -1) {
            return 'https://';
        }else{
            return 'http://';
        }
    }
    //通过播放地址判断使用http头
    function CheckHttp (Link) {
        if (Link.indexOf('.flv') != -1 || Link.indexOf('rtmp://') == 0 || Link.indexOf('.m3u8') != -1) { //播放方式为播放器播放
            return 'https://';
        }else{
            return 'http://';
        }
    }
    //获取地址
    function PlayVideoShare(mid ,sport) {
        var host = '{{$host}}';
        var url = '/match/live/url/match/' + mid + '_' + sport +'.json';
        $.ajax({
            url: url,
            type:'GET',
            dataType:'json',
            success:function(data){
                if (data.code == 0){
                    var preUrl;
                    if (GetHttp() == 'https://') { //如果当前地址是https，则只能使用https的player
                        preUrl = 'https://';
                        //preUrl = 'https://' + host + '/live/player.html?sport=' + sport + '&cid=' + data.cid;
                    }else{ //如果当前地址是http
                        if (data.play == 11) { //规定了播放方式，并为iframe方式，使用http
                            preUrl = 'http://';
                            //preUrl = 'http://' + host + '/live/player.html?sport=' + sport + '&cid=' + data.cid;
                        }else if (data.play >= 12) { //规定了播放方式，并为播放器播放，使用https
                            preUrl = 'https://';
                            //preUrl = 'https://' + host + '/live/player.html?sport=' + sport + '&cid=' + data.cid;
                        } else if (data.playurl) { //如果无规定，则要对playurl做判断
                            preUrl = CheckHttp(data.playurl);
                            //preUrl = CheckHttp(data.playurl) + host + '/live/player.html?sport=' + sport + '&cid=' + data.cid;
                        }else if (data.js){ //如果加密了，无playurl，用https
                            preUrl = 'https://';
                            //preUrl = 'https://' + host + '/live/player.html?sport=' + sport + '&cid=' + data.cid;
                        }
                    }
//                    preUrl = preUrl + host + '/live/player.html?sport=' + sport + '&cid=' + data.cid + "&type=" + data.type;
                    preUrl = preUrl + host + '/live/player/player-'+data.cid+'-'+data.type+'.html';
                    var MyFrame = document.getElementById('MyFrame');
                    MyFrame.setAttribute('allowfullscreen','true');
                    MyFrame.setAttribute('scrolling','no');
                    MyFrame.setAttribute('frameborder','0');
                    MyFrame.src = preUrl;
                }
            }
        })
    }

    window.onload = function () { //需要添加的监控放在这里
        var mid = '{{request('mid')}}';
        var sport = '{{request('sport')}}';
        if (mid && mid != '') {
            PlayVideoShare(mid, sport);
        }
        else {
            var str = window.location.pathname;
            var index = str .lastIndexOf("\/");
            str  = str .substring(index + 1, str .length);
            str = str.replace('.html','');
            var params = str.split("-");
            if (params.length == 3) {
                mid = params[1];
                sport = params[2];
                if (mid && mid != '') {
                    PlayVideoShare(mid, sport);
                }
            }
        }
    }
</script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</html>