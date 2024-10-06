
/* Render a chunk of an html page instead of a whole page in order to reduce page load.

The way it works: 

1- when user clicks on a button with attribute "data-get-chunk" we get the parameters (chunk name; chunk target; ans maybe some other parameters)

2- an ajax call get the apropriate template then place it in the target container

Ex:  get a chunk named plans (in backend) and place it (frontend) into a div #displayPlans when user clicks:

    <button data-get-chunk='{"name": "plans", "target": "#displayPlans" }>Show Pricing Plans</button>' --> this button will triger an ajax call to get pricing plan details display 

2 functions bellow to manage that:
    
    - first: bind the click event to tags with "data-get-chunk" attributes and get their params (name of the chunk; target name (where we place the chunk); + potential other parameters)

    - second function: actually do the ajax call and place the chunk in the appropriate place

 */

$(document).ready(function(){

    //Bind the click event; get chunk parameters; call the ajax function
    $("[data-get-chunk]").on('click', function (e){

        e.preventDefault();

        $(".loader-target").show(); //display a wait loader
        $(this).find(".loader-target").addClass("loader");

        dataChunk = $(this).data("get-chunk"); //getting params to send to backend
        dataParams = $(this).data("chunk-params");

        //console.log("dataChunk"+dataChunk);
        //console.log("dataParams"+dataParams);

        if(typeof(dataParams) == 'undefined' && dataParams == null){//if tag contains no other relevant params (dataParams) we set the value to null

            dataParams = null;

        }

        //this function actually makes the ajax call
        getChunk(dataChunk.name, dataChunk.target, dataParams); 
        
    }); 

   
    //Actually do the ajax call and place the chunk into the targeted container
    function getChunk(name, target, params){

        //console.log("getChunkName "+name);
        //console.log("getChunkTarget "+target);
        //console.log("getChunkParams "+JSON.stringify(params));

        $(target).html(""); //cleaning targeted container

        $.ajax({  

            url: getChunkRouteName, //defined as a global variable in the base template
            type: 'POST',   
            
            data: {
                "chunkName": name,
                "params": JSON.stringify(params),
            },

            async: true,  
            
            success: function(data, status) {

                $(".loader-target").hide(); //hide the wait loader
                $(target).html(data.html); //put the chunk in the targeted container

            },  

            error : function(data, textStatus, jqXHR) {  

                console.log(data);
            }  

        });

    }


}); 