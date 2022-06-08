

$(document).ready(function(){

  /* Closing search experience container */
  /* Warning : order of events matters (to do : factorize) */

  //when closing a search experience, we clear search proxies
  $(".js-close-search-experience, .sm-search-back-icon-container").click(function() {

    $("#navbar_md_search_input, #navbar_sm_search_input, #homepage-search-input").val('');

  });

  //search panel close button (except from homepage, and only on large screens from navbar searchbar)
  $(".js-close-search-experience").click(function() {

    $(".search-experience-container").hide();
    $("#main-body-container").show();

  });

  //same but from homepage body searchbar (main body container is now not hidden but 1px * 1px, so treatment is different)
  $(".js-close-search-experience").click(function() {

    $("#main-body-container").removeClass("visually-hidden");
    document.getElementById('homepage-search-container').scrollIntoView();

  });

  //create a random background color for default thumbnails
  function randomColor(){

    var colors = (['#884394', '#3f51b5', '#009688', '#42a346', '#ff9800', '#2d66ba', '#a58f4f', '#129d90', '#428392', '#263b78', '#878273', '#705c20', '#d24040', '#c1944e']);

    return colors[Math.floor(Math.random()*colors.length)];

  }


  //Format keywords (separate and add #)
  function formatKeywords(keywordsString){

    if(keywordsString != ''){
      return '#'+keywordsString.replace(/\s/g, '').replace(/,/g, ' #');
    }

    return keywordsString;    

  }

  //Initialize InfoBox for Google Map info box displays on marker click
  var o = new InfoBox({/*options*/}); //new


  //Initialize Algolia instant search with credentials

  const search = instantsearch({

    //indexName: app_env+'_presentation_bases',
    indexName: 'prod_presentation_bases', //new

    searchClient: algoliasearch(

      'Z7NO8ZLFH4',
      'b2d9ba779ea94f1c5f81d1b5751e267e'

    ),

  });


  //#searchbox div is the only search input recipient (.ais-SearchBox-input)
  search.addWidgets([

    instantsearch.widgets.searchBox({

      container: '#searchbox',

    }),

  ]);

  search.on('render', function () {//render fires when all widgets are rendered. This happens after every search request.

    
    //reset search input on mobile when clicking on black cross
    $(".js-reset-search-icon").click(function() {

      $('#navbar_sm_search_input').val('').focus();
      document.querySelector('#searchbox .ais-SearchBox-input').value = '';
      search.helper.setQuery(document.querySelector('#searchbox .ais-SearchBox-input').value).search();

    });

    //Below are some search input proxies. $('').on("input") event means : "each time a proxy has changed" 
    $("#navbar_md_search_input, #navbar_sm_search_input, #homepage-search-input").on("input", function() {

      $(".ais-InstantSearch").show(); //display search results panel

      //small screens : "reset search" button  (black cross icon) : appearance only if input is feeded
      if (!this.value) {
        $(".js-reset-search-icon-container").hide();
      } else {$(".js-reset-search-icon-container").show();}


      //put the proxy input value into "the only one true search input" (i.e. #searchbox .ais-SearchBox-input input)
      document.querySelector('#searchbox .ais-SearchBox-input').value = $(this).val();
      
      //then refresh algolia search
      search.helper.setQuery(document.querySelector('#searchbox .ais-SearchBox-input').value).search();


      // note : $("#homepage-search-input").on triggers scroll to top (see home/_search_container.html.twig) 


    });

  });


  search.addWidgets([

    instantsearch.widgets.geoSearch({

      container: '#maps',

      googleReference: window.google,

      initialZoom: 4,

      initialPosition: {

        lat: 47,
        lng: 3,

      },

      enableClearMapRefinement: false,

      customHTMLMarker: {

        events: {

          click: function(e) {

            console.log(e.item);

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


            //text container
            var textContent='<div class="text-container">';

            //presentation goal
            textContent += '<div class="goal-container">'+e.item.goal+'</div>';

            //presentation title
            if (e.item.title){
              textContent += '<div class="title-container">'+ e.item.title+'</div>';
            }

            textContent+='</div>'
          
            
            content += textContent+'</div></a>'; 

            o.getMap() && o.close(), o.setContent(content), o.open(e.map, e.marker)

          },

        },

      },


      templates: {
        HTMLMarker: '<p>Your Marker</p>',
      },


    }),


    //instantiate a search result filters panel (left panel)
    instantsearch.widgets.refinementList({

      container: '#filters-list',
      attribute: 'categories.descriptionFr',
      operator: 'and',
      
    }),


    //instantiate a search results panel (right panel)
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

    //instantiate a search results pagination
    instantsearch.widgets.pagination({
      container: '#pagination',
    }),

    instantsearch.widgets.configure({
      hitsPerPage: 8
    }),


  ]);

  // Sometimes we drag and drop some search results (example : admin search for presentation to highlight, see manage_select_presentations.html)

  if(typeof selection_instance !== 'undefined'){ //add a drag and drop capability to search results (see selection_manage.html).

    search.on('render', function () {// Whenever instant search is refreshed
      
      $('.ais-Hits-list').sortable({

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

          //changing dragged item wrapping tag (otherwise item disappear each time I click on an algolia pagination number)
          $(evt.item).replaceWith($('<div class="selected-item">').append($(evt.item).contents()).append('<button type"button" class="js-remove-elem">&times</button>'));

          // preparing an array to store "picked elements positions" by id
          var elementsPositions = [];

          //getting elements positions              
          $('.selected-items-panel a').each(function(index){
                          
            elementsPositions.push($(this).data('id'));
        
          });

          $.ajax({  

            url: pick_up_elements_route, // see manage_select_presentations.html
            type:       'POST',   
            dataType:   'json',
            data: {
                "selectionType": 'headlines',
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

 
