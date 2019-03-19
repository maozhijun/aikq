
function setPage () {
	$('#Right_part a.nba_part').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings('a.on').removeClass('on')

			$(this).parent().siblings('table.integral').css('display','none')
			.filter('.' + $(this).attr('forItem')).css('display','');
		}
	})
	$('#Left_part .el_con .page a').click(function(){
		if (!$(this).hasClass('on')) {
			$(this).addClass('on').siblings().removeClass('on');
			goPage($(this).html(),$(this).parents('.el_con'));
		}
	})
}

function goPage (num,target) {
	var Link = target.hasClass('history') ? ('https://api.aikanqiu.com/app/teamHistoryMatch/' + SportId + '/' + TeamId + '.json?page=' + num) : ('https://api.aikanqiu.com/app/teamRecentMatch/' + SportId + '/' + TeamId + '.json?page=' + num);

	$.ajax({
		url: Link,
		success: function(res){
			// console.log(res)
			var ArrDate = res.data;

			target.find('table tr').remove();

			for (var i = 0; i < ArrDate.length; i++) {
				var AddTR = '<tr><td><span>' + ArrDate[i].lname + '</span></td><td><span>' + setMyTime(ArrDate[i].time * 1000,'year') + '&nbsp;&nbsp;&nbsp;' + setMyTime(ArrDate[i].time * 1000,'time') + '</span></td>' +
					'<td class="host"><a href="/' + (FindLeagueName(ArrDate[i].sport,ArrDate[i].lid) ? FindLeagueName(ArrDate[i].sport,ArrDate[i].lid).name_en : 'other') + '/team' + ArrDate[i].sport + reTurnTeamId(ArrDate[i].hid) + '.html">' + ArrDate[i].hname + '</a></td>' +
					'<td class="vs">' + (ArrDate[i].status >= 0 ? (ArrDate[i].status == 0 ? ('vs') : ('<span class="living">直播中</span>')) : (ArrDate[i].hscore + ' - ' + ArrDate[i].ascore)) + '</td>' +
					'<td class="away"><a href="/' + (FindLeagueName(ArrDate[i].sport,ArrDate[i].lid) ? FindLeagueName(ArrDate[i].sport,ArrDate[i].lid).name_en : 'other') + '/team' + ArrDate[i].sport + reTurnTeamId(ArrDate[i].aid) + '.html">' + ArrDate[i].aname + '</a></td>' +
					'<td class="line">' + (ArrDate[i].status >= 0 ? (ArrDate[i].channels[0] ? '<a href="' + ArrDate[i].channels[0].live_url + '" class="live">高清直播</a>' : '') : (ArrDate[i].channels[0] ? '<a href="' + ArrDate[i].channels[0] + '" class="live">观看录像</a>' : '')) + '</td></tr>';


				target.find('table').append(AddTR)
			}

		}
	});
}










































