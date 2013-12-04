$(function() {
    $('.change-user-status').bind('click', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var label = $(this).find('.button-action-label');

        $.ajax({
            type: 		'post',
            dataType:	'html',
            async:      false,
            url:		url,
            success: function(status){
                label.html(status);
            }
        });
    });
});