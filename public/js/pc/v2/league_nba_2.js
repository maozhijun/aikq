

function setPage () {
	NowDate = null;

	$('#League_info dt').click(function(){
		$('#League_info dd').css('display',function(){
			return $(this).css('display') == 'none' ? '' : 'none'
		})
	})

	$('#Left_part .date button').click(function(){
		changeDate(this);
	})

	$('.player_rank .rank_tab_box p').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('.on').removeClass('on')
			$('.player_rank table').css('display','none').filter('.' + $(this).attr('forItem')).css('display','');
		}
	})

	Refresh()
}

function changeDate (obj) {
	var time = obj.getAttribute("time");
	var name = obj.getAttribute("name");
	var season = obj.getAttribute("season");
	var className = obj.className;
    NowDate = new Date(time * 1000);

    var start = "";
    var end = "";
    if (className == "left") {//前三天
        end = getYmd(NowDate);
        var cDate = calDate(NowDate, -3);
        start = getYmd(cDate);
	} else {//后三天
        start = getYmd(NowDate);
        var cDate = calDate(NowDate, 3);
        end = getYmd(cDate);
	}

	var url = window.jsonHostNew +"/"+name+"/basketball/schedule/"+season+"_"+start+"_"+end+".json";//
    var $obj = $("#Left_part .date button");
    $obj.attr("disabled", "disabled");

	$.ajax({
		"url": url,
		"dataType": "jsonp",
		"success": function (data) {
			if (data && data.schedule) {
				var dataStart = data.start;
				var dataEnd = data.end;

				var startCn = data.startCn;
                var endCn = data.endCn;

                var schedule = data.schedule;
				var html = "";
				$.each(schedule, function (time, matches) {
					var timeCn = time.split("-");
                    timeCn = timeCn[1] + "月" + timeCn[2] + "日";
                	html += '<p class="date_text">'+timeCn+'</p>';
					html += tableHtml(matches);
				});
				if (html != "") {
                    $("div.schedule_con").html(html);
				}
                $("#Left_part .date button.left").attr("time", dataStart);
                $("#Left_part .date button.right").attr("time", dataEnd);
                $("#Left_part .date p.con_text").html(startCn+"至"+endCn);
			} else {
				alert("请求数据失败");
			}
            $obj.removeAttr("disabled");
		},
		"error": function () {
            $obj.removeAttr("disabled");
		}
	});
}

function tableHtml(matches) {
	if (matches.length == 0) return "";
	var table = '<table class="match">';
    table += '<colgroup><col width="20%"><col><col width="15%"><col><col width="20%"></colgroup>';
	table += '<tbody>';
	var tr;
	var vs;
	var live;
	$.each(matches, function (index, match) {
		var hname = match["hname"];
		var aname = match["aname"];
		var time = match["time"];
		var status = match["status"];

        time = time.substr(11, 5);
        if (status == 0) {
            vs = "未开始";
		} else if (status > 0) {
        	vs = '<span class="living">直播中</span>';
		} else {
            vs = "已结束";
		}

        live = status >= 0 ? '<a class="live" target="_blank" href="'+match["detailUrl"]+'">观看直播</a>' : '';

        tr = '<tr>';
        tr += '<td>'+time+'</td>';
        tr += '<td class="host"><a target="_blank" href="'+match["hUrl"]+'">'+hname+'</a></td>';
        tr += '<td class="vs">'+vs+'</td>';
        tr += '<td class="away"><a target="_blank" href="'+match["aUrl"]+'">'+aname+'</a></td>';
        tr += '<td class="line">' + live + '</td>';
        tr += '</tr>';
        table += tr;
	});
    table += '</tbody>';
    table += '</table>';
    return table;
}

function calDate(date, day) {
	if (!date) return;
	var time = date.getTime();
	time = time + day * 24 * 60 * 60 * 1000;
	var newDate = new Date(time);
	return newDate;
}

function getYmd(date) {
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    month = month < 10 ? "0" + month : month;
    day = day < 10 ? "0" + day : day;
    return year + month + day;
}

/*用户加载，刷新最新列表*/
function Refresh () {
	// LeagueKeyword

	$.ajax({
		url: PubHeader + LeagueKeyword + '.json',
		success: function(res){
        	console.log(res)

        	ResetRightNews(res.articles)
        	ResetRightVideo(res.videos)
    	}
    });
}













































