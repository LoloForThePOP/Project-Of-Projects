
/* Render a chunk of an html page, thanks to an ajax call, in order to reduce a page load. */

$(document).ready(function(){

    // Bid some events to display a template chunk when appropriate (currently, only when clicking some elements, see bellow) (using above ajax function to request for the template chunk)
    
    /* Get html chunk when clicking on a button with data-get-chunk attribute (this attribute contains chunk name and target container)

    ex : <button data-get-chunk='{"name": "plans", "target": "#displayPlans" }>Show Pricing Plans</button>' --> this button will triger an ajax call to get pricing plan details display */

    $("[data-get-chunk]").on('click', function (e){

        e.preventDefault();


        $(".loader-target").show();
        $(this).find(".loader-target").addClass("loader");

        dataChunk = $(this).data("get-chunk");
        dataParams = $(this).data("chunk-params");

        console.log("dataChunk"+dataChunk);
        console.log("dataParams"+dataParams);

        if(typeof(dataParams) == 'undefined' && dataParams == null){

            dataParams = null;

        }

        getChunk(dataChunk.name, dataChunk.target, dataParams);
        
    }); 

    // render a named template into a targeted html container (target : a jquery selector) (ex : render "pricing plans" into "#plans-container" div).

    function getChunk(name, target, params){

        console.log("getChunkName "+name);
        console.log("getChunkTarget "+target);
        console.log("getChunkParams "+JSON.stringify(params));

        $(target).html("");

        //$(target).append('<div id="ajax-loader" class="my-3 text-center loader"></div>');

        $.ajax({  

            url: getChunkRouteName, //defined as a global variable in the base template
            type:       'POST',   
            
            data: {
                "chunkName": name,
                "params": JSON.stringify(params),
            },
            async: true,  
            
            success: function(data, status) {

                $(".loader-target").hide();
                $(target).html(data.html);

            },  

            error : function(data, textStatus, jqXHR) {  

                console.log(data);
                //alert('Get chunk ajax request failed.');  
            }  

        });

    }



}); 