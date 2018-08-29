function loadNews() {
    if (!window.curPage)  window.curPage = 1;
    if (!window.loadPage) window.loadPage = false;
    window.curPage++;
    var isLoading = window.loadPage;
    if (!isLoading && window.curPage <= window.lastPage) {
        window.loadPage = true;
        var url = location.pathname + "/page" + window.curPage + ".html";
        $.ajax({
            "url": url,
            "dataType": "html",
            "success": function (html) {
                if (html.length > 0) {
                    $("#Content a:last").after(html);
                    window.loadPage = false;
                }
            },
            "error": function () { }
        });
    }
}