function loadNews() {
    if (!window.curPage)  window.curPage = 1;
    if (!window.loadPage) window.loadPage = false;

    var curPage = parseInt(window.curPage);
    var isLoading = window.loadPage;
    if (!isLoading) {
        window.loadPage = true;
        var url = "/m/news/page" + (curPage + 1) + ".html";
        $.ajax({
            "url": url,
            "dataType": "html",
            "success": function (html) {
                $("#Content a:last").after(html);
            },
            "error": function () { }
        });
    }
}