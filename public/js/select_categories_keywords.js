$(document).ready(function(){

    /* Manage Categories */

    $("#cat-grid .cat-button").on("click", function(event){ 

        var catId = $(this).attr('id');

        $(this).toggleClass("selectedCategory");

        $(this).find(".bt-right-col").append('<div class="check-mark">✅</div>');

        $.ajax({  

            url: ajaxUpdateCategoriesRoute,
            type:       'POST',   
            dataType:   'json',
            data: {
                "category-id": catId,
            },
            async:      true,  
            
            success: function(data, status) {  
               
                $(".check-mark").remove();

                customEvents.dispatchEvent(

                    new CustomEvent('presentationChangedOnRemote', {

                        detail: {

                          structure: 'categories'

                        }

                    })

                );
                
            },  

            error : function(xhr, textStatus, errorThrown) {  
                //alert('Ajax request failed.');  
            }  
        });  

    });  


    /* Manage Keywords */

    /* Processus : each time keywords are changed (created , removed, reordered) :
    
        - we refresh an hidden input (function refreshHidden())
        - we send hidden input value to update remotely (function updateRemote())
        - we send a js "presentation changed event" to refresh ui

    */


    // displayed keyword template
    var keywordWrapper = '<div class="keyword-wrapper"><div class="left-col d-inline-block"></div><div class="right-col d-inline-block"><div class="remove-keyword">&times</div></div></div>';

    // refresh an hidden div that contains keywords concatened in a string and separated with a comma.
    // + call to update remote accordingly

    function refreshHidden(){

        var string ='';

        $( ".keyword-wrapper .left-col" ).each(function( index ) {

            string = string+$(this).text()+',';
            
        });

        string = string.substring(0, string.length - 1)

        $( "#keywords-string" ).text(string);

        updateRemote();

    }

    // ajax request to update keywords on remote

    function updateRemote(){

        var keywords = $( "#keywords-string" ).text();

        $("#keywords-display").append('<div class="loader"></div>');
        
        $.ajax({  

            url: ajaxUpdateKeywordsRoute,
            type:       'POST',   
            dataType:   'json',
            data: {
                "keywords": keywords,
            },

            async: true,  
            
            success: function(data, status) {

                $(".loader").remove();

                $("#keywords-display").append('<span class="check-mark">✅</span>');

                $(".check-mark").fadeOut(1500);

                customEvents.dispatchEvent(

                    new CustomEvent('presentationChangedOnRemote', {
                        detail: {
                          structure: 'keywords'
                        }
                    })

                );
                  
                        
            },  

            error : function(xhr, textStatus, errorThrown) {  
                alert('Une erreur est survenue. Veuillez recharger la page.');  
            }  
        }); 
    }


    // case user manualy type a keyword without autocomplete

    $( "#tag-submit-button" ).click(function( event ) {
        
        addKeyword($("#keywordsInput").val());

        event.preventDefault();
    });

    
    // case user add a keyword using autocomplete menu 

    $("#keywordsInput").autocomplete({

        select: function( event, ui ) {

            addKeyword(ui.item.value);                   

            return false;

        },

    });


    // Call to wikipedia API to feed autocomplete suggestions

    $("#keywordsInput").autocomplete({

        source: function (request, response) {

            $.ajax({

                // Wikipedia API url link
                url:
                "https://fr.wikipedia.org/w/api.php",
                dataType: "jsonp",
                data: {
                    action: "opensearch",
                    // Output format
                    format: "json",
                    search: request.term,
                    namespace: 0,

                    // Maximum number of result
                    // to be shown
                    limit: 10,
                },
                success: function (data) {

                    response(data[1]);
                },
            });
        },
    });



    
    // add a keyword in displayed list

    function addKeyword(text){

        $("#keywords-display").append(keywordWrapper);

        $("#keywords-display .keyword-wrapper:last-child").find(".left-col").html(text);

        refreshHidden();

        $("#keywordsInput").val('');
        $("#keywordsInput").autocomplete( "close" );
        $("#keywordsInput").focus();

    }

    // Remove a keyword

    $("#keywords-display").on("click", ".remove-keyword", function(){

        $(this).parent().parent().remove();

        refreshHidden();

    });

    // Sort keywords capability

    $('#keywords-display').sortable({

        animation: 150,

        ghostClass: 'blue-background-class',

        filter: ".disabled",

        onMove: function (evt) {
            return evt.related.className.indexOf('disabled') === -1;
        },

        onEnd: function (evt) {

            refreshHidden();

        },

    });




});
