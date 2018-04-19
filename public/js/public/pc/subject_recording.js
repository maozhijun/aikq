 function createPageHtml(pageId) {
    var $pageDiv = $("#" + pageId);
    var curPage = $pageDiv.attr("curPage");
    var lastPage = $pageDiv.attr("lastPage");
    curPage = parseInt(curPage);
    lastPage = parseInt(lastPage);

    var pageHtml = "";
    if (lastPage === 1) return;

    if (lastPage > 7 && curPage !== 1) {
        pageHtml += '<a class="up">上一页</a>';
    }
    pageHtml += '<a ' + (curPage === 1 ? 'class="on"' : '') + '>1</a>';
    var index, showBtn = 7;
    if (lastPage - curPage <= 3) {
        index = lastPage - showBtn;
    } else {
        index = curPage - 3;
    }
    index = index <= 1 ? 2 : index;
     if (index > 2) {
         pageHtml += '<p>...</p>';
     }
     for(var f_index = 0; f_index < showBtn; f_index++) {
         if (index >= lastPage) continue;
         var css = '';
         if (curPage === index) {
             css = 'class="on"';
         }
         pageHtml += '<a ' + css + '>' + index++ + '</a>';
     }
    if (index < lastPage) {
        pageHtml += '<p>...</p>';
    }
    pageHtml += '<a ' + (curPage == lastPage ? 'class="on"' : '') + '>' + lastPage + '</a>';
    pageHtml += '<p><span>' + curPage + '</span>/' + lastPage + '</p>';
    if (lastPage > 7 && curPage !== lastPage) {
        pageHtml += '<a class="down">下一页</a>';
    }
    $pageDiv.html(pageHtml).show();
}
function bindPageA(pageId) {
    var pageDiv = $("#" + pageId);
    pageDiv.find('a:not([class])').click(function () {
        var page = this.innerHTML;
        var url = location.href;
        url = url.replace(/\d+\.html/, page + '.html');
        location.href = url;
    });

    var curPage = pageDiv.attr("curPage");
    curPage = parseInt(curPage);
    pageDiv.find('a[class]').click(function () {
        var css = this.className;
        var url = location.href;
        if (css === 'up') {
            url = url.replace(/\d+\.html/, (curPage - 1) + '.html');
            location.href = url;
        } else if (css === 'down') {
            url = url.replace(/\d+\.html/, (curPage + 1) + '.html');
            location.href = url;
        }
    });
}
function bindType() {
    $('div.labelbox button').click(function () {
        var id = this.getAttribute('id');
        var url = '/live/subject/videos/' + id + '/1.html';
        location.href = url;
    });
}