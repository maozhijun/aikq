//翻页到页底
function scrollBottom (endFunc) {
    var ClientHeight,BodyHeight,ScrollTop;
    if(document.compatMode == "CSS1Compat"){
        ClientHeight = document.documentElement.clientHeight;
    }else{
        ClientHeight = document.body.clientHeight;
    }

    BodyHeight = document.body.offsetHeight;

    ScrollTop = getPageScroll()[1];

    if (BodyHeight - ScrollTop - ClientHeight < 20) {
        endFunc();
    }
}

function loadVideos() {
    if (!window.curPage)  window.curPage = 1;
    if (!window.loadPage) window.loadPage = false;

    var curPage = parseInt(window.curPage);
    var isLoading = window.loadPage;
    if (!isLoading) {
        window.loadPage = true;
        var url = location.href;
        url = url.replace(/\d+\.html/, (curPage + 1) + ".json");
        var week = ['周日','周一','周二','周三','周四','周五','周六'];
        $.ajax({
            "url": url,
            "dataType": "json",
            "success": function (json) {
                if (json.page) {
                    window.curPage = json.page.curPage;
                } else {
                    window.curPage = window.curPage + 1;
                }
                if (json.matches) {
                    var match_array, v_day, v_date, v_title, d_html = '';
                    match_array = json.matches;

                    var lastDay = $("p.day:last").attr('day');
                    var lastDefault = $("div.default:last");
                    $.each(match_array, function (day, matches) {
                        if (day == lastDay) {
                            $.each(matches, function (v_index, match) {
                                v_date = new Date(match.time * 1000);
                                v_title = match.hname + " " + match.hscore + " - " + match.ascore + " " + match.aname;
                                var html = '<a href="' + subjectVideoLink(match.id) + '">';
                                html += '<p class="time">' + match.lname + '&nbsp;&nbsp;' + v_date.getHours() + ':' + v_date.getMinutes() + '</p>';
                                html += '<p class="other">' + v_title + '</p>';
                                html += '</a>';
                                lastDefault.append(html);
                            });
                        } else {
                            var date = new Date(day);
                            d_html += '<div class="default">';
                            d_html += '<p class="day" day="'+ day + '">' + day + '&nbsp;&nbsp;' + week[date.getDay()] + '</p>';

                            $.each(matches, function (v_index, match) {
                                v_date = new Date(match.time * 1000);
                                v_title = match.hname + " " + match.hscore + " - " + match.ascore + " " + match.aname;
                                d_html += '<a href="' + subjectVideoLink(match.id) + '">';
                                d_html += '<p class="time">' + match.lname + '&nbsp;&nbsp;' + v_date.getHours() + ':' + v_date.getMinutes() + '</p>';
                                d_html += '<p class="other">' + v_title + '</p>';
                                d_html += '</a>';
                            });
                            d_html += '</div>';
                        }
                    });
                    lastDefault.after(d_html);
                }
                window.loadPage = false;
            },
            "error": function () { }
        });
    }
}

function subjectVideoLink(id) {
    var idStr = id + '';
    if (idStr.length < 4) {
        return "";
    }
    var first = idStr.substr(0, 2);
    var second = idStr.substr(2, 4);
    return "/m/live/subject/video/" + first + "/" + second + "/" + id + ".html";
}

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
}