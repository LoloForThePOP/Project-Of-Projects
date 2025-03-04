
/*

  Propon uses Algolia to index and search Propon searchable contents.
  This script handles some frontend search functionnalities. 

  Warning:   
  
    I used some amateur bad quality tricks in order to get the search experience I wanted.
    I apologies for the resulting poor code comprehension; logic; and clarity: my bad they can be improved
    Help for improvments is welcome.


*/

$(document).ready(function(){

  /* Close search experience container */
  /* Warning: order of events matters */

  //when closing a search experience, we clear search proxies and clear geosearch map display
  $(".js-close-search-experience, .sm-search-back-icon-container").click(function() {

    $("#navbar_md_search_input, #navbar_sm_search_input, #homepage-search-input").val('');
    
    //to do : factorize code
    $('.ais-GeoSearch').hide(); 
    $('.js-hide-geosearch-map').hide();
    $('.js-show-geosearch-map').show();

  });

  //Hide search panel layer when user clicks on close button

  $(".js-close-search-experience").click(function() {

    //When search is not done from homepage body (this concerns header navbar searchbar on large screens)
    $(".search-experience-container").hide();
    $("#main-body-container").show();
    
    //When search is done from homepage body proxy search bar, we "hide homepage body" with 1px * 1px dimensions (display: hidden mess things up), so treatment is different to show it again
    $("#main-body-container").removeClass("visually-hidden");
    document.getElementById('homepage-search-container').scrollIntoView();

  });
  

  //Search results management: create a random background color for default thumbnails (ex: project presentation has no image to show has a thumbnail)
  function randomColor(){

    var colors = (['#884394', '#3f51b5', '#009688', '#42a346', '#ff9800', '#2d66ba', '#a58f4f', '#129d90', '#428392', '#263b78', '#878273', '#705c20', '#d24040', '#c1944e']);

    return colors[Math.floor(Math.random()*colors.length)];

  }

  //Search results management: format keywords to human readable keywords (separate and add some # before)
  function formatKeywords(keywordsString){

    if(keywordsString != ''){
      return '#'+keywordsString.replace(/\s/g, '').replace(/,/g, ' #');
    }

    return keywordsString;    

  }

  //Search results on a Google Map: Info box displays on marker click: Initialize InfoBox "script"
  var o = new InfoBox({/*options*/});


    const searchClient = algoliasearch(

      'Z7NO8ZLFH4',
      'b2d9ba779ea94f1c5f81d1b5751e267e'

    );

  //Initialize Algolia instant search app with credentials

  const search = instantsearch({

    indexName: 'prod_presentation_bases',

    searchClient,

  });




  // Setting an input that will trigger algolia searchs
  
  search.addWidgets([

    instantsearch.widgets.searchBox({

      container: '#searchbox', //#searchbox div is the only search input recipient we use for Propon (algolia search script then gives it the following classname : ais-SearchBox-input)

    }),

  ]);

  //render fires when all widgets are rendered. This happens after every search request.
  search.on('render', function () {



    //reset search input on mobile when user clicks on black cross icon
    $(".js-reset-search-icon").click(function() {

      $('#navbar_sm_search_input').val('').focus();
      document.querySelector('#searchbox .ais-SearchBox-input').value = '';
      search.helper.setQuery(document.querySelector('#searchbox .ais-SearchBox-input').value).search();

    });



    //When a search input proxy value changes, we show the search result panel.
    $("#navbar_md_search_input, #navbar_sm_search_input, #homepage-search-input").on("input", function() { //means "each time a proxy value has changed" 

      $(".ais-InstantSearch").show(); //display the search results panel (still empty of results)

      //small screens : "reset search" button  (black cross icon): we display this icon only if input is feeded
      if (!this.value) {
        $(".js-reset-search-icon-container").hide();
      } else {$(".js-reset-search-icon-container").show();}

      window.scrollTo(0, 0); //view goes to the top.

      //put the proxy input value into "the only one true search bar" (i.e. a div with id="searchbox")
      document.querySelector('#searchbox .ais-SearchBox-input').value = $(this).val();
      
      //then refresh algolia search (putting the value above is not enough, we must actually execute the search)
      search.helper.setQuery(document.querySelector('#searchbox .ais-SearchBox-input').value).search();

      //note : $("#homepage-search-input").on triggers scroll to top (see home/_search_container.html.twig) 

    });

  });



  //Handling search results appearence on a Google Map

  search.addWidgets([

    instantsearch.widgets.geoSearch({

      container: '#maps',

      googleReference: window.google,

      initialZoom: 4,

      mapOptions: {

        minZoom: 2,

      },

      initialPosition: {

        lat: 58.2276,
        lng: 2.21,
        
      },

      enableClearMapRefinement: false,

      builtInMarker: {

        createOptions(item) {

          return {

            title: item.name,

          };

        },

        events: { //here we create an html structure with its content that appears when user clicks on a Google Map search result marker so that user get basic informations about the project represented by this marker (i.e. we create an infobox)

          click: function(e) {

            content = '<a class="link-wrapper" href="https://www.propon.org/'+e.item.stringId+'" data-id="'+e.item.id+'" target="_blank"><div>';

            //preparing card's thumbnail
            var imgContent;

            //if we have an image path we use it
            if (e.item.cache && e.item.cache.thumbnailAddress){

              imgContent = '<div class="img-container"><img class="infobox-img" src="'+e.item.cache.thumbnailAddress+'" align="left" alt="Thumbnail" /></div>';

            }

            else {//creating a default thumbnail

              imgContent= '<div class="avatar-square-rounded avatar-80 mx-auto" style="background-color:#428392;"><span class="avatar-initial avatar-initial-80">'+e.item.goal.charAt(0).toUpperCase()+'</span></div>';
            
            }

            //card's image container + feeding it
            content += imgContent;


            //begining of text content wrapping (project goal + title)
            var textContent='<div class="text-container">';

            //presentation goal
            textContent += '<div class="goal-container">'+e.item.goal+'</div>';

            //presentation title
            if (e.item.title){
              textContent += '<div class="title-container">'+ e.item.title+'</div>';
            }

            textContent+='</div>'
            //end of text content wrapping
          
            
            content += textContent+'</div></a>'; 

            o.getMap() && o.close(), o.setContent(content), o.open(e.map, e.marker) // from infobox.js

          },

        },

      },


    }),

    //instantiating an Algolia search result filter results panel (left panel)
    instantsearch.widgets.refinementList({

      container: '#filters-list',
      attribute: 'categories.descriptionFr',
      operator: 'and',
      limit: 30,
      
    }),


    //instantiating an algolia search results panel ("just" a right panel showing results, we got to instanciate it)
    instantsearch.widgets.hits({

      container: '#hits',

      templates: { //search results cards template
        
        item:function(data) {

          //wrapping card with a casual link to appropriate project page
          output = '<a href="'+domain+data.stringId+'" data-id="'+data.id+'" class="hit-link-wrap">';

          //preparing card's thumbnail
          var imgContent;

            //if we have an image path we use it
            if (data.cache && data.cache.thumbnailAddress){

              imgContent = '<img class="hit-img" src="'+data.cache.thumbnailAddress+'" align="left" alt="Thumbnail" />';

            }

            else {//creating a default thumbnail

              imgContent= '<div class="avatar-square-rounded avatar-80 mx-auto" style="background-color:'+randomColor()+';"><span class="avatar-initial avatar-initial-80">'+data.goal.charAt(0).toUpperCase()+'</span></div>';
            
            }

          //card's image container + feeding it
          output += '<div class="hit-img-ctn">'+imgContent+'</div>';

          //preparing card's text content

          var textContent;

          //presentation goal
          textContent = '<div class="hit-goal">'+data._highlightResult.goal.value+'</div>';

          var titleContent = '';
          if (data.title){titleContent = data._highlightResult.title.value;}
          textContent += '<div class="hit-title">'+titleContent+'</div>';

          var keywordsContent = '';
          if (data.keywords){keywordsContent = data._highlightResult.keywords.value;}
          textContent += '<div class="hit-keywords">'+formatKeywords(keywordsContent)+'</div>';

          //card's text container + feeding it with text content + end of link wrapper
          output += '<div class="hit-txt-ctn"><div class="hit-flex-ctn">'+textContent+'</div></div></a>'; 
          
          return output

        },

        empty: '<div>Désolé, aucun résultat n\'a été trouvé pour la recherche "{{ query }}".</div>'

      },
      
    }),

    //instantiating a search results pagination container
    instantsearch.widgets.pagination({
      container: '#pagination',
    }),


    //configuring some properties about search results
    instantsearch.widgets.configure({
      hitsPerPage: 8
    }),


  ]);

  // Sometimes it's usefull to drag and drop some search results (ex: an admin searches for some presentations to highlight, he can pick up some presentation thumbnails and drag and drop them from search results (see select_presentations\manage.html.twig for the html template))

  //NOTE: we can drag and drop items from search results to add new items (this capability is handled here and the ajax call to store resulting picked elements content / positions evolution is handled here too). We can also sort the already picked elements with drag and drop or delete some of them (this capability and the ajax call for backend storage is handled select_presentations\manage.html.twig)

  if(typeof selection_instance !== 'undefined'){ //when we are in a user interface whereby we can drag and drop elements (i.e. a selection instance) we add a drag and drop capability to search results so that we can pick them and drop them (selection_instance is a variable declared in select_presentations\manage.html.twig)

    search.on('render', function () {// Whenever instant search is refreshed
      
      $('.ais-Hits-list').sortable({ //Adds sortable capability (= a javascript that simplifies drag and drop (sortable.js))

        animation: 150,

        group: {

          name:'results-to-selection', // groups which share same names are "drag and drop linked".
          put: false // Do not allow items to be put into the parent list

        },

        sort: false, // Disable sorting in parent list

        ghostClass: 'blue-background-class',

        filter: ".disabled",

        onMove: function (evt) {

          return evt.related.className.indexOf('disabled') === -1;

        },

        onRemove: function (evt) {//each time a presentation is picked from search results.

          //changing dragged item wrapping tag (otherwise the item disappear each time I click on an algolia pagination number)
          $(evt.item).replaceWith($('<div class="selected-item">').append($(evt.item).contents()).append('<button type"button" class="js-remove-elem">&times</button>'));

          // preparing an array to store "picked elements positions" by id (so that we can store on backend the selection in a db or a json file)
          var elementsPositions = [];

          //getting elements positions              
          $('.selected-items-panel a').each(function(index){
                          
            elementsPositions.push($(this).data('id'));
        
          });

          //picking and dropping an item from search results implies picked elements content / positions is updated (so we do an ajax call to store updated positions in backend)

          $.ajax({  

            url: pick_up_elements_route, // backend route to store selected items positions (specific route to handle ajax call) (note: this variable is declared in select_presentations\manage.html.twig)
            type:       'POST',   
            dataType:   'json',
            data: {
                "jsonElementsPosition": elementsPositions,
            },

            async: true,  
            
            success: function(data, status) {
                    
            },  

            error : function(xhr, textStatus, errorThrown) {  
              alert('Une erreur est survenue.');  
            }  

          }); 

        },

      });
      
    });

  }

  search.start();

});

 
