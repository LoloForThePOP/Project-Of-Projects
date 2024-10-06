	
  // Allows to zoom on a image when user place the mouse pointer over this image
  
  // Thanks to https://www.w3schools.com/howto/howto_js_image_zoom.asp
  function imageZoom(imgParentWrapperId, resultID) {

    var img, lens, result, cx, cy, timeout, imgWrapper,

    imgWrapper = document.getElementById(imgParentWrapperId);

    result = document.getElementById(resultID);

    if (imgWrapper==null){return;} //case slide do not contains a image (for example it contains a video)
    
    else{

      img = imgWrapper.getElementsByTagName('img')[0];

      /* Create lens: */
      lens = document.createElement("DIV");
      lens.setAttribute("class", "img-zoom-lens");

      /* Insert lens: */
      img.parentElement.insertBefore(lens, img);

      /* Calculate the ratio between result DIV and lens: */
      cx = result.offsetWidth / lens.offsetWidth;
      cy = result.offsetHeight / lens.offsetHeight;

      /* Set background properties for the result DIV */
      result.style.backgroundImage = "url('" + img.src + "')";
      result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";



      /* Execute a function when someone moves the cursor over the image, or the lens: */
      lens.addEventListener("mousemove", moveLens);
      img.addEventListener("mousemove", moveLens);
      
      lens.addEventListener("mouseout", closeZoom);
      img.addEventListener("mouseout", closeZoom);

      /* And also for touch screens:
      lens.addEventListener("touchmove", moveLens);
      img.addEventListener("touchmove", moveLens); */

      lens.style.opacity="0";
    
    }


    function moveLens(e) {

      var pos, x, y;

      /* Prevent any other actions that may occur when moving over the image */
      e.preventDefault();

      /* Make lens appears */
      lens.style.opacity="0.2";

      /* Get the cursor's x and y positions: */
      pos = getCursorPos(e);

      /* Calculate the position of the lens: */
      x = pos.x - (lens.offsetWidth / 2);
      y = pos.y - (lens.offsetHeight / 2);
      
      /* Prevent the lens from being positioned outside the image: */
      if (x > img.width - lens.offsetWidth) {x = img.width - lens.offsetWidth; }
      if (x < 0) {x = 0;}
      if (y > img.height - lens.offsetHeight) {y = img.height - lens.offsetHeight;}
      if (y < 0) {y = 0;}

      /* Set the position of the lens: */
      lens.style.left = x + "px";
      lens.style.top = y + "px";

      /* Display what the lens "sees": */
      result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";


      result.style.marginLeft = "auto";

      
      
    }

    function closeZoom(e) {

      result.style.marginLeft = "-10000px";
      
    }

    function getCursorPos(e) {


      var a, x = 0, y = 0;
      e = e || window.event;

      /* Get the x and y positions of the image: */
      a = img.getBoundingClientRect();

      /* Calculate the cursor's x and y coordinates, relative to the image: */
      x = e.pageX - a.left;
      y = e.pageY - a.top;

      /* Consider any page scrolling: */
      x = x - window.pageXOffset;
      y = y - window.pageYOffset;
      return {x : x, y : y};

    }

  }


  function killImageZoom(){

    const elements = document.getElementsByClassName("img-zoom-lens");

    while(elements.length > 0){

      elements[0].parentNode.removeChild(elements[0]);

    }

  }
