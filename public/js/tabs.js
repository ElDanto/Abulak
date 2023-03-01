jQuery(document).ready(function () {
    $('.coincidences-tab').on('click', function (event) {
        var tab = $('.coincidences-tab');
        var tab_content = $('.coincidences-content_item');
        var tab_id = $(this).attr('id');
    
        event.preventDefault();

        tab_content.removeClass('active');
        tab.removeClass('active');
        $(this).addClass('active');

        $('#tab-' + tab_id).addClass('active');
    });

    return true;
});