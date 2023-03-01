jQuery(document).ready(function () {
    $('#search-article_btn').on('click', function (){
        
        var search_phrase =  $('#search-phrase').val();
        var articles_block = $('.articles');
        
        if(search_phrase == '') {
            $('#search-article_errors').html('Введите поисковую фразу');
            return true;
        }
        if(search_phrase.match(/\'|\"|\`|\(|\)|\;|\:|\,|\.|\!|\?|\^|\%\|\$|\+|\-/)) {
            $('#search-article_errors').html('Введен запрещенный символ. Измените поисковую фразу');
            return true;
        }

        articles_block.html("<span class=\"waiting-for\">Пожалуйста подождите, идет поиск и обработка подходящей станицы...</span>");

        $('.search-panel_errors').empty();
        $.ajax({
            type: "POST",
            url: "/import/",
            data: {data : search_phrase},
            success: function (html) {
                articles_block.html(html);
            },
        });
    });

    $('#search-keyword_btn').on('click', function (){
        
        var keyword =  $('#keyword').val().toLowerCase();
        var сoincidences_block = $('.сoincidences');

        if(keyword == ''){
            $('#search-keyword_errors').html('Введите поисковую фразу');
            return true;
        }
        if(keyword.match(/\'|\"|\`|\(|\)|\;|\:|\,|\.|\!|\?|\^|\%\|\$|\+|\-/)) {
            $('#search-keyword_errors').html('Введен запрещенный символ. Измените поисковую фразу');
            return true;
        }

        сoincidences_block.addClass('active');
        сoincidences_block.html("<span class=\"waiting-for\">Пожалуйста подождите, идет поиск совпадений...</span>");

        $('.search-panel_errors').empty();
        $.ajax({
            type: "POST",
            url: "/search/",
            data: {data : keyword},
            success: function (html) {
                сoincidences_block.html(html);
            }
        });
    });
    return false;
    
});