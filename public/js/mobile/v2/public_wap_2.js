//屏幕可视区域高度，基本不变
var Window_H = document.documentElement.clientHeight;

//获取链点参数
function GetQueryString(str,href) {
    var href = href ? href : location.href;
    var rs = new RegExp("([\?&])(" + str + ")=([^&#]*)(&|$|#)", "gi").exec(href);
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

//判断是否到底部
function checkToBottom () {
  var SCT = getPageScroll()[1];
  var BH = $(document).height();

  if (SCT + Window_H + 20 >= BH) {
    return true;
  }else{
    return false;
  }
}



/*广告关闭*/
$(function(){
  $('.app_full_con button.close').click(function(){
    $(this).parents('.app_full_con').remove();
  })
})