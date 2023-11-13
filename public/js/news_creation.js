$(document).ready(function(){

    $("#covering-footer").append($(".news-form-struct"));
    $(".news-form-struct").css("display","block");

    $(".proxy-news-input, .js-footer-news").on('click', function (){

        populateFooterNews();
        
        // If user have several presentation, we post news targeted presentation.
        var presentationId = $(this).data("pp-id");
        $("#news_presentationId").val(presentationId);

    });


    function populateFooterNews() {

        $("#covering-footer").addClass("displayFlex");
        $("#covering-footer").show();
        tinymce.execCommand('mceFocus',false,'news_textContent');

    }

});