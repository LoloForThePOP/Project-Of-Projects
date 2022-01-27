
/* Render a chunk of an html page, thanks to an ajax call, in order to reduce a page load. */

$(document).ready(function(){


    // render a named template into a targeted html container (target : a jquery selector) (ex : render "pricing plans" into "#plans-container" div).

    function getChunk(name, target, params){

        $(target).html("");

        $(target).append('<div id="ajax-loader" class="my-3 text-center loader"></div>');

        $.ajax({  

            url: getChunkRouteName, //defined as a global variable in the base template
            type:       'POST',   
            dataType:   'json',
            
            data: {
                "chunkName": name,
                "params": params,
            },
            async: true,  
            
            success: function(data, status) {

                $("#ajax-loader").remove();
                $(target).html(data.html);

            },  

            error : function(xhr, textStatus, errorThrown) {  
                alert('Ajax request failed.');  
            }  

        });

    }

    // Bid some events to display a template chunk when appropriate (currently, only when clicking some elements, see bellow) (using above ajax function to request for the template chunk)
    
    /* Get html chunk when clicking on a button with data-get-chunk attribute (this attribute contains chunk name and target container)

    ex : <button data-get-chunk='{"name": "plans", "target": "#displayPlans" }>Show Pricing Plans</button>' --> this button will triger an ajax call to get pricing plan details display */

    $("[data-get-chunk]").on('click', function (){

        dataChunk = $(this).data("get-chunk");
        dataParams = $(this).data("chunkParams");

        //console.log(dataChunk);
        //console.log(dataParams);

        if(typeof(dataParams) == 'undefined' && dataParams == null){

            dataParams = null;

        }

        getChunk(dataChunk.name, dataChunk.target, dataParams);
        
    }); 


}); 