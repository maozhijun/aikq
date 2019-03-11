
//公共传参头部
var PubHeader = '//api.dlfyb.com/json/pc/comboData/';

//获取联赛名2皆匹配规则
var CanGetTeamLink = false;
$.getScript("//static.dlfyb.com/js/pc/v2/id_2_league_name.js",function(){CanGetTeamLink = true});

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

//时间设定
function setMyTime (time,type) {
  var newTime = new Date(time)
  
  var newYear = newTime.getFullYear(),
  newMonth = (newTime.getMonth() + 1) > 9 ? (newTime.getMonth() + 1) : '0' + (newTime.getMonth() + 1),
  newDay = newTime.getDate() > 9 ? newTime.getDate() : '0' + newTime.getDate(),
  newHour = newTime.getHours() > 9 ? newTime.getHours() : '0' + newTime.getHours(),
  newMinute = newTime.getMinutes() > 9 ? newTime.getMinutes() : '0' + newTime.getMinutes()

  if (type == 'time') {
    return newHour + ':' + newMinute
  }else if (type == 'year') {
    return newYear + '-' + newMonth + '-' + newDay
  }else if (type == 'date') {
    return newMonth + '-' + newDay
  }else if (type == 'month') {
    return newMonth + '-' + newDay + ' ' + newHour + ':' + newMinute
  }else{
    return newYear + '-' + newMonth + '-' + newDay + ' ' + newHour + ':' + newMinute
  }
}

//球队id少于4位补0
function reTurnTeamId (ID) {
    var newID = '' + ID + ''
    if (newID.length >= 4) {
        return newID
    }else{
        var Count = 4 - newID.length;
        for (var i = 0; i < Count; i++) {
            newID = '0' + newID;
        }

        return newID
    }
}


/*共用重置左边part内容*/
function ResetLeftMatch (MatchObj,count) { //match是对象，不是数组
    var Key = Object.keys(MatchObj);
    count = count ? count : 8;

    if (Key.length == 0) {
        $('#Left_part .more_live_con').remove();
    }else{
        $('#Left_part .more_live_con .match tr').remove();
    }

    var Append = 0;
    for (var i = 0; i < Key.length; i++) {
        if (!(MatchObj[Key[i]].status == '-1') && Append < count) {

            var Target = MatchObj[Key[i]],

            newLive = $('<tr><td><img src="//static.dlfyb.com/img/pc/v2/' + (Target.sport == 1 ? 'icon_foot_light_opaque.png' : 'icon_basket_light_opaque.png') + '" class="type"></td>' +
                          '<td><span>' + Target.league_name + '</span></td>' + 
                          '<td><span>' + setMyTime(Target.time,'date') + '<br/>' + setMyTime(Target.time,'time') + '</span></td>' + 
                          '<td class="host"><a href="/' + (FindLeagueName(Target.sport,Target.lid) ? FindLeagueName(Target.sport,Target.lid).name_en : 'other') + '/team' + Target.sport + reTurnTeamId(Target.hid) + '.html">' + Target.hname + '</a></td>' +
                          '<td class="vs">' + (Target.isMatching ? '<span class="living">直播中</span>' : (Target.status == '-1' ? '已结束' : 'vs')) + '</td>' + 
                          '<td class="away"><a href="/' + (FindLeagueName(Target.sport,Target.lid) ? FindLeagueName(Target.sport,Target.lid).name_en : 'other') + '/team' + Target.sport + reTurnTeamId(Target.aid) + '.html">' + Target.aname + '</a></td>' +
                          '<td class="line"></td></tr>')

            for (var j = 0; j < Target.channels.length; j++) {
                newLive.find('.line').append('<a href="' + Target.channels[j].live_url + '" class="live">' + Target.channels[j].name + '</a>');
            }

            $('#Left_part .more_live_con .match').append(newLive)
        }
    }
}

function ResetLeftMatch_2 (MatchObj,count) { //match是对象，不是数组
    var Key = Object.keys(MatchObj);
    count = count ? count : 8;

    if (Key.length == 0) {
        $('#Left_part .more_live_con').remove();
    }else{
        $('#Left_part .more_live_con .match tr').remove();
    }

    var Append = 0;
    for (var i = 0; i < Key.length; i++) {
        if (!(MatchObj[Key[i]].status == '-1') && Append < count) {

            var Target = MatchObj[Key[i]],

            newLive = $('<tr><td>' + setMyTime(Target.time,'date') + '<br/><span>' + setMyTime(Target.time,'time') + '</span></td>' + 
                        '<td class="host"><a href="/' + (FindLeagueName(Target.sport,Target.lid) ? FindLeagueName(Target.sport,Target.lid).name_en : 'other') + '/team' + Target.sport + reTurnTeamId(Target.hid) + '_index_1.html">' + Target.hname + '</a></td>' + 
                        '<td class="vs">' + (Target.isMatching ? '<span class="living">直播中</span>' : (Target.status == '-1' ? '已结束' : 'vs')) + '</td>' + 
                        '<td class="away"><a href="/' + (FindLeagueName(Target.sport,Target.lid) ? FindLeagueName(Target.sport,Target.lid).name_en : 'other') + '/team' + Target.sport + reTurnTeamId(Target.aid) + '_index_1.html">' + Target.aname + '</a></td>' + 
                        '<td class="line"></td></tr>')

            for (var j = 0; j < Target.channels.length; j++) {
                newLive.find('.line').append('<a href="' + Target.channels[j].live_url + '" class="live">' + Target.channels[j].name + '</a>');
            }

            $('#Left_part .more_live_con .match').append(newLive)
        }
    }
}

function ResetLeftNews (newsArr){
    if (newsArr.length == 0) {
        $('#Left_part .el_con .news_list').parent().remove();
    }else{
        $('#Left_part .el_con .news_list').html('');
    }

    for (var i = 0; i < (newsArr.length < 10 ? newsArr.length : 10); i++) {
        var newNews = '<div class="news_con"><a href="' + newsArr[i].link + '">' + 
                      '<p class="img_box"><img src="' + newsArr[i].cover + '"></p>' + 
                      '<h5>' + newsArr[i].title + '</h5>' + 
                      '<p class="other_info">' + setMyTime(newsArr[i].update_at,'date') + '</p>' +
                      '<p class="tag_list"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>利物浦</span><span>萨拉赫</span></p></a></div>';

        $('#Left_part .el_con .news_list').append(newNews)
    }
}

function ResetLeftVideo (videoArr){
    if (videoArr.length == 0) {
        $('#Left_part .el_con .video_list').parent().remove();
    }else{
        $('#Left_part .el_con .video_list').html('');
    }

    for (var i = 0; i < (videoArr.length < 12 ? videoArr.length : 12); i++) {
        var newVideo = '<div class="list_item"><a href="' + videoArr[i].link + '">' + 
                      '<p class="img_box"><img src="' + videoArr[i].image + '"></p>' + 
                      '<div class="title_text"><p><span>' + videoArr[i].title + '</span></p></div></a></div>';

        $('#Left_part .el_con .video_list').append(newVideo)
    }
}

function ResetLeftRecord (recordArr){
    if (recordArr.length == 0) {
        $('#Left_part .el_con .record_list').parent().remove();
    }else{
        $('#Left_part .el_con .record_list tr').remove();
    }

    for (var i = 0; i < (recordArr.length < 10 ? recordArr.length : 10); i++) {
        var newRecord = '<tr><td><span>' + setMyTime(recordArr[i].match.time) + '</span></td>' +
                        '<td class="host"><a href="/' + (FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid) ? FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid).name_en : 'other') + '/team' + recordArr[i].match.sport + reTurnTeamId(recordArr[i].match.hid) + '_index_1.html">' + recordArr[i].match.hname + '</a></td>' +
                        '<td class="vs">' + recordArr[i].match.hscore + ' - ' + recordArr[i].match.ascore + '</td>' +
                        '<td class="away"><a href="/' + (FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid) ? FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid).name_en : 'other') + '/team' + recordArr[i].match.sport + reTurnTeamId(recordArr[i].match.aid) + '_index_1.html">' + recordArr[i].match.aname + '</a></td>' + 
                        '<td class="line"><a href="' + recordArr[i].link + '" class="live">观看录像</a></td></tr>';

        $('#Left_part .el_con .record_list').append(newRecord)
    }
}


/*共用重置右边part内容*/
function ResetRightNews (newsArr) {
    if (newsArr.length == 0) {
        $('#Right_part .con_box .news').html('<div class="noList_con">暂无相关资讯</div>');
    }else{
        $('#Right_part .con_box .news').html('');
    }

    for (var i = 0; i < (newsArr.length < 12 ? newsArr.length : 12); i++) {
        var newNews = '';
        if (i == 0 || i == 1) {
            newNews = '<a href="' + newsArr[i].link + '" class="img_news"><p class="img_box"><img src="' + newsArr[i].cover + '"></p><h3>' + newsArr[i].title + '</h3></a>';
        }else{
            newNews = '<a href="' + newsArr[i].link + '" class="text_new"><h4>' + newsArr[i].title + '</h4></a>';
        }

        $('#Right_part .con_box .news').append(newNews)
    }

    $('#Right_part .con_box .news').addClass('show')
}

function ResetRightVideo (videoArr) {
    if (videoArr.length == 0) {
        $('#Right_part .con_box .video').html('<div class="noList_con">暂无相关视频</div>');
    }else{
        $('#Right_part .con_box .video').html('');
    }

    for (var i = 0; i < (videoArr.length < 10 ? videoArr.length : 10); i++) {
        var newVideo = '<div class="video_item"><a href="' + videoArr[i].link + '"><p class="img_box"><img src="' + videoArr[i].image + '"></p><p class="text_box">' + videoArr[i].title + '</p></a></div>';

        $('#Right_part .con_box .video').append(newVideo)
    }

    $('#Right_part .con_box .video').addClass('show')
}

function ResetRightMatch (MatchObj,count) { //match是对象，不是数组
    var Key = Object.keys(MatchObj);
    count = count ? count : 6;

    if (Key.length == 0) {
        $('#Right_part .con_box .live').html('<div class="noList_con">暂无相关直播</div>');
    }else{
        $('#Right_part .con_box .live').html('');
    }

    var Append = 0;
    for (var i = 0; i < Key.length; i++) {
        if (!(MatchObj[Key[i]].status == '-1') && Append < count) {

            var Target = MatchObj[Key[i]];

            var newLive = $('<div class="live_item"><p class="live_match_info">' + Target.league_name + '<span>' + setMyTime(Target.time,'month') + '</span></p>' + 
                          '<div class="live_match_team"><p class="team"><span><a href="/' + (FindLeagueName(Target.sport,Target.lid) ? FindLeagueName(Target.sport,Target.lid).name_en : 'other') + '/team' + Target.sport + reTurnTeamId(Target.hid) + '_index_1.html">' + Target.hname + '</a></span></p>' + 
                          '<p class="vs">' + (Target.isMatching ? '<span>直播中</span>' : (Target.status == '-1' ? '已结束' : 'VS')) + '</p>' + 
                          '<p class="team"><span><a href="/' + (FindLeagueName(Target.sport,Target.lid) ? FindLeagueName(Target.sport,Target.lid).name_en : 'other') + '/team' + Target.sport + reTurnTeamId(Target.aid) + '_index_1.html">' + Target.aname + '</a></span></p></div>' + 
                          '<div class="live_match_line"></div></div>');

            for (var j = 0; j < Target.channels.length; j++) {
                newLive.find('.live_match_line').append('<a href="' + Target.channels[j].live_url + '">' + Target.channels[j].name + '</a>');
            }

            $('#Right_part .con_box .live').append(newLive)

            Append++
        }
    }

    $('#Right_part .con_box .live').addClass('show')
}

function ResetRightRecord (recordArr) {
    if (recordArr.length == 0) {
        $('#Right_part .con_box .record').html('<tr><td><div class="noList_con">暂无相关视频</div></td></tr>');
    }else{
        $('#Right_part .con_box .record tr').remove()
    }

    for (var i = 0; i < (recordArr.length < 10 ? recordArr.length : 10); i++) {
        var newRecord = '<tr><td class="time">' + setMyTime(recordArr[i].match.time,'date') + '<br/>' + setMyTime(recordArr[i].match.time,'time') + '</td><td>' + 
                       '<p><a href="/' + (FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid) ? FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid).name_en : 'other') + '/team' + recordArr[i].match.sport + reTurnTeamId(recordArr[i].match.hid) + '_index_1.html"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/30.png">' + recordArr[i].match.hname + '</a><span>' + recordArr[i].match.hscore + '</span></p>' + 
                       '<p><a href="/' + (FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid) ? FindLeagueName(recordArr[i].match.sport,recordArr[i].match.lid).name_en : 'other') + '/team' + recordArr[i].match.sport + reTurnTeamId(recordArr[i].match.aid) + '_index_1.html"><img src="http://mat1.gtimg.com/sports/nba/logo/1602/30.png">' + recordArr[i].match.aname + '</a><span>' + recordArr[i].match.ascore + '</span></p></td>' + 
                       '<td><a href="' + recordArr[i].link + '">观看录像</a></td></tr>';

        $('#Right_part .con_box .record').append(newRecord)
    }

    $('#Right_part .con_box .record').addClass('show')
}






















