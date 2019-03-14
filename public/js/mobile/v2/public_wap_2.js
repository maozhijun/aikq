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



/*广告关闭*/
$(function(){
  $('.app_full_con button.close').click(function(){
    $(this).parents('.app_full_con').remove();
  })
})