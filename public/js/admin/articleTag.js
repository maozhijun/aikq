function findLeagueTag(sport, resultId) {
    if (sport == "") {
        var html = "<option value=''>请选择</option>";
        $("#" + resultId).html(html);
        return;
    }
    var data = {"sport": sport, "level": 2};
    $.ajax({
        "url": "/admin/tags/find",
        "data": data,
        "success": function (json) {
            if (json && json.code == 200) {
                var leagues = json.data;
                var html = "<option value=''>请选择</option>";
                $.each(leagues, function (index, obj) {
                    html += "<option value='" + obj.tid + "' level='" + obj.level + "' id='" + obj.id + "'>" + obj.name + "</option>";
                });
                $("#" + resultId).html(html);
            } else {
                alert("获取赛事标签失败");
            }
        },
        "error": function () {
            alert("获取赛事标签失败");
        }
    });
}
function findTeam(lid, sportId, resultId) {
    var sport = $("#" + sportId).val();
    var data = {"sport": sport, "lid": lid};
    $.ajax({
        "url": "/admin/tags/teams/find",
        "data": data,
        "success": function (json) {
            if (json && json.code == 200) {
                var leagues = json.data;
                var html = "<option value=''>请选择</option>";
                $.each(leagues, function (index, obj) {
                    html += "<option value='" + obj.id + "' lid='" + obj.lid + "'>" + obj.name + "</option>";
                });
                $("#" + resultId).html(html);
            } else {
                alert("获取球队标签失败");
            }
        },
        "error": function () {
            alert("获取球队标签失败");
        }
    });
}

function addTag(thisBtn, level, resultId) {
    var $prev = $(thisBtn).prev();
    var $target = $("#" + resultId);
    if (level == 4) {
        var player = $prev.val();
        if (player == "") {
            alert("请先填写球员名称");
            return;
        }
        //球员标签
        //查看是否已经存在标签
        var $li = $target.find("li[player=" + player + "]");
        if ($li.length > 0) {
            alert("球员标签已存在");
            return;
        }
        var text = "<span>" + player + "</span>";
        var delBtn = "<a class='btn btn-xs btn-danger' onclick='delTag(this);'>删除</a>";
        var liHtml = "<li player='" + player + "'>" + text + delBtn + "</li>";
        $target.append(liHtml);
    } else if (level == 2) {
        //赛事标签
        var $option = $prev.find("option:selected");
        var id = $option.attr("id");
        var tid = $option.val();
        if (tid == "") {
            alert("请选择赛事");
            return;
        }
        var $li = $target.find("#" + id);
        if ($li.length > 0) {
            alert("赛事标签已存在");
            return;
        }
        var text = "<span>" + $option.text() + "</span>";
        var delBtn = "<a class='btn btn-xs btn-danger' onclick='delTag(this);'>删除</a>";
        var liHtml = "<li id='" + id + "' tid='" + tid + "'>" + text + delBtn + "</li>";
        $target.append(liHtml);
    } else if (level == 3) {
        //球队标签
        var $option = $prev.find("option:selected");
        var id = $option.val();
        if (id == "") {
            alert("请选择球队");
            return;
        }
        var $li = $target.find("#" + id);
        if ($li.length > 0) {
            alert("球队标签已存在");
            return;
        }
        var lid = $option.attr("lid");
        var text = "<span>" + $option.text() + "</span>";
        var delBtn = "<a class='btn btn-xs btn-danger' onclick='delTag(this);'>删除</a>";
        var liHtml = "<li id='" + id + "'>" + text + delBtn + "</li>";
        $target.append(liHtml);
    }
    //alert(formatTags("match_tag", "team_tag", "player_tag"));
}

function formatTags(mid, tid, pid) {
    //赛事标签处理
    var $matchLi = $("#" + mid + " li:gt(0)");
    var matches = [];
    $.each($matchLi, function (index, li) {
        var name = $(li).find("span").html();
        var obj = {"tag_id": li.id, "name": name, "level": 2};
        matches.push(obj);
    });

    //球队标签处理
    var $teamLi = $("#" + tid + " li:gt(0)");
    var teams = [];
    $.each($teamLi, function (index, li) {
        var tag_id = li.getAttribute("tag_id") == null ? "" : li.getAttribute("tag_id");
        var name = $(li).find("span").html();
        var obj = {"id": li.id, "name": name, "tag_id": tag_id, "level": 3};
        teams.push(obj);
    });

    //球员标签处理
    var $playerLi = $("#" + pid + " li:gt(0)");
    var players = [];
    $.each($playerLi, function (index, li) {
        var tag_id = li.getAttribute("tag_id") == null ? "" : li.getAttribute("tag_id");
        var name = $(li).find("span").html();
        var obj = {"id": li.id, "name": name, "tag_id": tag_id, "level": 4};
        players.push(obj);
    });

    var tag = {"match": matches, "team": teams, "player": players};
    return JSON.stringify(tag);
}

function delTag(thisBtn, relationId) {
    var $thisBtn = $(thisBtn);
    if (relationId) {
        if (!confirm("是否确认删除标签？")) return;
        $thisBtn.button("loading");
        //删除数据库关系
        $.ajax({
            "url": "/admin/tags/relation/del",
            "data": {"id": relationId},
            "success": function (data) {
                if (data) {
                    alert(data.message);
                    if (data.code == 200) {
                        $thisBtn.parent().remove();
                        return;
                    }
                }
                $thisBtn.button("reset");
            },
            "error": function () {
                alert("删除失败");
                $thisBtn.button("reset");
            }
        });
    } else {
        //直接删除标签
        var $li = $thisBtn.parent().remove();
    }
}