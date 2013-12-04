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
                $( ".button-action-label").not(this).html('Not selected');
                label.html(status);
            }
        });
    });
});