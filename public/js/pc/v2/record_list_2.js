
var DateTop = 0;

function setPage () {
	DateTop = $('#Date').offset().top;

    $('#Date .def_content a.all').click(function(){
        $(this).addClass('on');
        $('#Content .el_con').css('display','').filter('.add_con').css('display','none');
    })
	
	$('input[name="date"]').datepicker({format:"yyyy-mm-dd",language: "zh-CN"}).change(function(){
        var Time = new Date($(this).val())
        reloadList(Time)
    });

	if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
    }).blur(function() {
        var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur();
    };
}

function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}

$(window).scroll(function(){
	var ScT = $(window).scrollTop();

	if (ScT > DateTop) {
		$('#Date').addClass('fix');
	}else{
		$('#Date').removeClass('fix');
	}
})


function reloadList (Time) {
    var TargetDate = setMyTime(Time,'year');
    // console.log(TargetDate)

    $('#Content .el_con').css('display','none');
    $('#Date a.on').removeClass('on');

    if($('#' + TargetDate).length != 0){
        $('#' + TargetDate).css('display','');
    }else{
        $.ajax({
            url: 'http://cms.aikanqiu.com/api/recordData/' + TargetDate,
            success: function(res){
                // console.log(res[TargetDate])

                var newList = $('<div id="' + TargetDate + '" class="el_con add_con"><div class="header"><h3><p>' + TargetDate.split('-')[0] + '年' + TargetDate.split('-')[1] + '月' + TargetDate.split('-')[2] + '日</p></h3></div>' +
                    '<table><col width="8.2%"><col width="16.6%"><col width="9.8%"><col><col width="15%"><col><col width="20%"></table></div>');

                if (res[TargetDate].records.length > 0) {
                    for (var i = 0; i < res[TargetDate].records.length; i++) {
                        var obj = res[TargetDate].records[i],
                            newRecord = $('<tr type="' + (obj.sport == '1' ? 'football' : (obj.sport == '2' ? 'basketball' : '')) + '">' +
                                '<td><img class="icon" src="' + (obj.sport == '1' ? 'https://static.dlfyb.com/img/pc/v2/icon_foot_light_opaque.png' : (obj.sport == '2' ? 'https://static.dlfyb.com/img/pc/v2/icon_basket_light_opaque.png' : '')) + '"></td>' +
                                '<td>' + obj.lname + '</td><td>' + setMyTime (obj.time,'time') + '</td>' +
                                '<td><a href="' + obj.hurl + '">' + obj.hname + '</a></td>' +
                                '<td>' + obj.hscore + ' - ' + obj.ascore + '</td>' +
                                '<td><a href="' + obj.aurl +  '">' + obj.aname + '</a></td><td class="channel"><a href="' + obj.url + '">观看录像</a></td></tr>')

                        newList.find('table').append(newRecord)
                    }
                }else{
                    var Empty = $('<tfoot><tr><td colspan="7">当天暂无比赛录像</td></tr></tfoot>');

                    newList.find('table').append(Empty)
                }

                $('#Content').append(newList);
            }
        });
    }
}
