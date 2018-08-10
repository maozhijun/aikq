/**
 * Created by maozhijun on 17/3/24.
 */

function isWeChatBrowser() {
    return /micromessenger/.test(navigator.userAgent.toLowerCase());
}

function isApp() {
    return /liaogou168/.test(navigator.userAgent.toLowerCase());
}

////////////自定义微信分享/////////////////////
function customShare(desc, title, url, img, code) {
    if (!isWeChatBrowser()) {
        return;
    }
    var shareUrl = url == '' ? location.href.split('#')[0] : url;
    if (code !== undefined && code != "") {
        if (shareUrl.search('\\?') < 0) {
            shareUrl = shareUrl + "?agent_code=" + code;
        } else {
            shareUrl = shareUrl + "&agent_code=" + code;
        }
    }
    var wx_share_params = {};
    wx_share_params.desc = desc == '' ? '英雄莫问出处，总得有个去处。转会窗口，容许我跳！个！槽！' : desc;
    wx_share_params.title = title == '' ? "国际足坛夏季转会风云榜" : title;
    wx_share_params.url = shareUrl;
    wx_share_params.img = img == '' ? 'http://www.aikq.cc/img/mobile/image_tit_n.jpg' : img;

    if (window.wx !== undefined) {
        setShareParam();
    } else {
        $.getScript("//res.wx.qq.com/open/js/jweixin-1.0.0.js",
        function () {
            var apis = 'onMenuShareTimeline,onMenuShareAppMessage,onMenuShareQQ,onMenuShareWeibo';
            var url = encodeURIComponent(location.href.split('#')[0]);
            var debug = false;
            // var signUrl = '//mp.liaogou168.com/member/wechat/api/jsSign?apis=' + apis + '&url=' + url + '&debug=' + debug;
            var signUrl = 'http://mp.dlfyb.com/api/wechat/jsSign?apis=?apis=' + apis + '&url=' + url + '&debug=' + debug;
            $.ajax({
                url: signUrl,
                dataType: 'jsonp',
                success: function (data) {
                    wx.config(data);
                }
            });
            if (window.wx !== undefined) {
                setShareParam();
            }
        });
    }
    function setShareParam() {
        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: wx_share_params.desc,
                link: wx_share_params.url,
                imgUrl: wx_share_params.img
            });
            wx.onMenuShareAppMessage({
                title: wx_share_params.title,
                desc: wx_share_params.desc,
                link: wx_share_params.url,
                imgUrl: wx_share_params.img
            });
            wx.onMenuShareQQ({
                title: wx_share_params.title,
                desc: wx_share_params.desc,
                link: wx_share_params.url,
                imgUrl: wx_share_params.img
            });
            wx.onMenuShareWeibo({
                title: wx_share_params.title,
                desc: wx_share_params.desc,
                link: wx_share_params.url,
                imgUrl: wx_share_params.img
            });
        })
    }
}