
  $(document).ready(function(){


    $(".js-close-search-experience").click(function() {

      $(".search-experience-container").hide();
      $("#main-body-container").show();

    });


  function randomColor(){

    var colors = (['#884394', '#3f51b5', '#009688', '#42a346', '#ff9800', '#2d66ba', '#a58f4f', '#129d90', '#428392', '#263b78', '#878273', '#705c20', '#d24040', '#c1944e']);

    return colors[Math.floor(Math.random()*colors.length)];

  }

  function formatKeywords(keywordsString){

    if(keywordsString != ''){
      return '#'+keywordsString.replace(/\s/g, '').replace(/,/g, ' #');
    }

    return keywordsString;    

  }
  
  const search = instantsearch({
    indexName: app_env+'_presentation_bases',
    searchClient: algoliasearch(
      'Z7NO8ZLFH4',
      'b2d9ba779ea94f1c5f81d1b5751e267e'
    ),
  });

   
  search.on('render', function () {   

    $("#navbar_md_search_input, #navbar_sm_search_input").on("input", function() {

      $(".ais-InstantSearch").show();

      document.querySelector('#searchbox .ais-SearchBox-input').value = $(this).val();

      search.helper.setQuery(document.querySelector('#searchbox .ais-SearchBox-input').value).search();
      
      $("#main-body-container").hide();

    });

  });

  search.addWidgets([

    instantsearch.widgets.searchBox({

      container: '#searchbox',

    }),
    
    instantsearch.widgets.refinementList({

      container: '#filters-list',
      attribute: 'categories.descriptionFr',
      operator: 'and',
      
    }),


    instantsearch.widgets.hits({

      container: '#hits',

      templates: { /* Twig verbatim is used to avoid curly brackets (braces) conflicts */

        
        item:function(data) {

          output = '<a href="'+domain+data.stringId+'" class="hit-link-wrap">';

          var imgContent;

            if (data.cache && data.cache.thumbnailAddress){

              imgContent = '<img class="hit-img" src="'+data.cache.thumbnailAddress+'" align="left" alt="Thumbnail" />';

            }

            else {

              imgContent= '<div class="avatar-square-rounded avatar-80 mx-auto" style="background-color:'+randomColor()+';"><span class="avatar-initial avatar-initial-80">'+data.goal.charAt(0).toUpperCase()+'</span></div>';
            
            }

          output += '<div class="hit-img-ctn">'+imgContent+'</div>';

          output += '<div class="hit-txt-ctn"><div class="hit-flex-ctn">'
            
          output += '<div class="hit-goal">'+data._highlightResult.goal.value+'</div>';

          var titleContent = '';
          if (data.title){titleContent = data._highlightResult.title.value;}
          output += '<div class="hit-title">'+titleContent+'</div>';

          var keywordsContent = '';
          if (data.keywords){keywordsContent = data._highlightResult.keywords.value;}
          output += '<div class="hit-keywords">'+formatKeywords(keywordsContent)+'</div>';

          output += '</div></div></a>'; //end of text container + link wrapper
          
          return output


        },


        empty: '<div>Désolé, aucun résultat n\'a été trouvé pour la recherche "{{ query }}".</div>.'

      },
      
    }),

    instantsearch.widgets.pagination({
      container: '#pagination',
    }),

    instantsearch.widgets.configure({
      hitsPerPage: 8
    }),

  ]);
  
  search.start();

});

 
