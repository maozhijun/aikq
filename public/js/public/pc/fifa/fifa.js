function setPage () {
    setNews();
    setGroup();
    setGroup2Top();

    setInterval(function(){
        setFocus()
    },5000)
}
$(window).resize(function() {
  setNews();
  setGroup();
});
$(window).scroll(function () {
    var scroll = $(document).scrollTop();
    if (scroll < $('#A_Group').offset().top) {
        $('#Group .tab').attr('site','A');
    }else if (scroll < $('#B_Group').offset().top){
        $('#Group .tab').attr('site','B');
    }else if (scroll < $('#C_Group').offset().top){
        $('#Group .tab').attr('site','C');
    }else if (scroll < $('#D_Group').offset().top){
        $('#Group .tab').attr('site','D');
    }else if (scroll < $('#E_Group').offset().top){
        $('#Group .tab').attr('site','E');
    }else if (scroll < $('#F_Group').offset().top){
        $('#Group .tab').attr('site','F');
    }else if (scroll < $('#G_Group').offset().top){
        $('#Group .tab').attr('site','G');
    }else{
        $('#Group .tab').attr('site','H');
    }

    if (scroll > $('#Group .tab').offset().top - 90) {
        $('#Group .simulation').css('display','block');
    }else{
        $('#Group .simulation').css('display','');
    }
});



function setFocus () {
    if ($('#Focus a:last')[0] != $('#Focus a.on')[0]) {
        var On = $('#Focus a.on');
        On.removeClass('on');
        On.next().addClass('on');
    }else{
        $('#Focus a.on').removeClass('on');
        $('#Focus a:first').addClass('on');
    }
}


function setNews () {
    $('#News .imgList').width(parseInt($('#News .con').width() - $('#News dl').width()-1) + 'px');

    $('#News .right .tab .item').click(function(){
        if (!$(this).hasClass('on')) {
            $('#News .right ul').css('display','none');
            $('#News .right .tab .item').removeClass('on');

            $('#News .right ul.' + $(this).attr('for')).css('display','');
            $(this).addClass('on');
        }
    })
}

function setGroup () {
    $('#Group p.team').width(parseInt(($('#Group li').width() - 198) / 2) + 'px')
}

function setGroup2Top () {
    $('#Group .tab p').click(function(){
        $('html,body').animate({scrollTop: $('#' + $(this).attr('for')).offset().top - 120}, 500);
    })
}