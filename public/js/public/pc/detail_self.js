function initLineChannel(url, mid, sport, adName, adUrl) {
    var $line = $("#Info p.line");
    $.ajax({
        "url": url,
        "type": "get",
        "dataType": "json",
        "success": function (channels) {
            if (channels) {
                var html = "";
                var btnIndex = 0;
                $.each(channels, function (index, channel) {
                    var chId = channel.ch_id;
                    var name = channel.name;
                    var type = channel.type;
                    var player = channel.player;
                    var link = "";
                    if (player == 16) {
                        link = channel.link;
                    } else {
                        link = window.LHB_URL + '/live/spPlayer/player-' + mid +  '-' + sport + '.html?btn=' + (btnIndex++);
                    }
                    // else if (player == 11) {
                    //     link = window.LHB_URL + '/live/iframe/player-' + chId +  '-' + type + '.html';
                    // } else {
                    //     link = window.LHB_URL + '/live/player/player-' + chId +  '-' + type + '.html';
                    // }
                    //var onclick = "onclick=\"ChangeChannel('" + link + "', this)\"";
                    //html += "<button id=\"" + chId + "\" " + onclick + " >" + name + "</button>";
                    html += "<a href='" + link + "' target='_blank'>" + name + "</a>";
                });
                if (adName) {
                    html += "<a href=\"" + adUrl + "\" target=\"_blank\" style=\"border-color: #d24545; background: #d24545; color: #fff;\">" + adName + "</a>";
                }
                if (html != "") {
                    //html = "<span>直播线路：</span>" + html;
                    $line.html(html);
                }
            }
            LoadVideo();
        },
        "error": function () {
            LoadVideo();
        }
    });
}