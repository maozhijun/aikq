function setPage () {
    $('p[for]').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).siblings('p.on').removeClass('on');
            $(this).addClass('on');

            $('#Match, #Player, #Info').css('display','none');
            $('#' + $(this).attr('for')).css('display','');

        }
    })
}
