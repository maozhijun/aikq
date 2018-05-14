function setPage () {
    $('p[for]').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).siblings('p.on').removeClass('on');
            $(this).addClass('on');

            $('#group, #goal, #assists').css('display','none');
            $('#' + $(this).attr('for')).css('display','');

            $("html,body").animate({scrollTop:0}, 300);
        }
    })
}
