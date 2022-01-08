
/* Render a chunk of an html page, thanks to an ajax call, in order to reduce a page load. */

$(document).ready(function(){


    // render a named template into a targeted html container (ex : render "pricing plans" into "#plans-container" div).

    function getChunk(name, target){ //target : a jquery selector

        $(target).append('<div id="ajax-loader" class="my-3 text-center loader"></div>');

        $.ajax({  

            url: getChunkRouteName, //defined as a global variable in base template
            type:       'POST',   
            dataType:   'json',
            
            data: {
                "chunkName": name,
            },
            async: true,  
            
            success: function(data, status) {

                $("#ajax-loader").remove();
                $(target).append(data.html);

            },  

            error : function(xhr, textStatus, errorThrown) {  
                alert('Ajax request failed.');  
            }  

        });

    }

    // Bid some events to display chunks when appropriate (using above ajax function) (currently, only when clicking some elements, see bellow)
    
    /* Get html chunk when clicking on a button with data-get-chunk attribute (this attribute contains chunk name and target container)

    ex : <button data-get-chunk='{"name": "plans", "target": "#displayPlans" }>Show Pricing Plans</button>' --> this button will triger an ajax call to get pricing plan details display */

    $("[data-get-chunk]").on('click', function (){

        data = $(this).data("get-chunk");

        getChunk(data.name, data.target);
        
    }); 


}); 