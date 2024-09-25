
//Allow to add a show more button when vertical content is too long.
//Give the class hide-too-long to the container you want to partially hide.


// Thanks to Trevor Nestman; https://stackoverflow.com/questions/5270227/

// Create Variables
var allOSB = [];
var mxh = '';

window.onload = function() {
  // Set Variables
  allOSB = document.getElementsByClassName("hide-too-long");
  
  if (allOSB.length > 0) {
    mxh = window.getComputedStyle(allOSB[0]).getPropertyValue('max-height');
    mxh = parseInt(mxh.replace('px', ''));
    
    // Add read-more button to each hide too long section
    for (var i = 0; i < allOSB.length; i++) {
      var el = document.createElement("a");
      el.innerHTML = "Afficher +";
      el.setAttribute("type", "button");
      el.setAttribute("class", "read-more sm-display-none");
      el.setAttribute("src", "#");
      
      insertAfter(allOSB[i], el);
    }
  }

  // Add click function to buttons
  var readMoreButtons = document.getElementsByClassName("read-more");
  for (var i = 0; i < readMoreButtons.length; i++) {
    readMoreButtons[i].addEventListener("click", function() { 
      revealThis(this);
    }, false);
  }
  
  // Update buttons so only the needed ones show
  updateReadMore();
}
// Update on resize
window.onresize = function() {
  updateReadMore();
}

// show only the necessary read-more buttons
function updateReadMore() {
  if (allOSB.length > 0) {
    for (var i = 0; i < allOSB.length; i++) {
      if (allOSB[i].scrollHeight > mxh + 50) {
        if (allOSB[i].hasAttribute("style")) {
          updateHeight(allOSB[i]);
        }
        allOSB[i].nextElementSibling.className = "read-more btn btn-primary btn-sm mt-2";
      } else {
        allOSB[i].nextElementSibling.className = "read-more sm-display-none";
      }
    }
  }
}

function revealThis(current) {
  var el = current.previousElementSibling;
  if (el.hasAttribute("style")) {
    current.innerHTML = "Afficher +";
    el.removeAttribute("style");
  } else {
    updateHeight(el);
    current.innerHTML = "Réduire";
  }
}

function updateHeight(el) {
  el.style.maxHeight = el.scrollHeight + "px";
}

// thanks to karim79 for this function
// http://stackoverflow.com/a/4793630/5667951
function insertAfter(referenceNode, newNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}