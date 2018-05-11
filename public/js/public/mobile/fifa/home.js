function setPage () {
    $('p[for]').click(function(){
        if (!$(this).hasClass('on')) {
            $(this).siblings('p.on').removeClass('on');
            $(this).addClass('on');

            var Target = $('#' + $(this).attr('for'));
            Target.siblings('.matchList').css('display','none');
            Target.css('display','');

            $("html,body").animate({scrollTop:0}, 300);
        }
    })
}
