function setPage () {
    $('#Player .item').click(function(){
        if(!$(this).hasClass('on')){
            $('#Player .item').removeClass('on');
            $('#Player tbody').css('display','none');

            $(this).addClass('on');
            $('#Player tbody.' + $(this).attr('for')).css('display','');
        }
    })
}










