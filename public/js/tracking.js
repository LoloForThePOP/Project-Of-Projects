
/**
 * Handles user tracking (cookies; mixpanel; etc)
 */


// global variables

var showCookiesInvite; // Boolean that tells if we display the select cookies panel (when he has already visited the website the answer is no)
var acceptCookies; // user choice: "accept" or "dismiss" cookies

// checking in user browser local storage if user once made a decision...
const cookiesPreferences = localStorage.getItem('cookiesPreferences') ? localStorage.getItem('cookiesPreferences') : null;

// ...and routing accordingly to cookies invite or cookies resolution
if (cookiesPreferences == "accept" || cookiesPreferences == "reject") { //case user once made a decision
    showCookiesInvite = false;
    cookiesResolution(cookiesPreferences);
}
else{ //case no trace of user previous website visit: we display cookies panel
    showCookiesInvite = true;
}



// activating / desactivating third parties cookies according to user choice
function cookiesResolution(cookiesPreferences) {

    if(cookiesPreferences == "accept"){
                
        // Hotjar Tracking Code
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:2214943,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');

    }

    else{ //user reject cookies

        mixpanel.opt_out_tracking(); //opt out from mixpanel

        gtag('consent', 'default', { // opt out from google analytics
            'ad_storage': 'denied',
            'analytics_storage': 'denied'
        });
          

    }

};

/* Mixpanel tracked Events on Website

Mixpanel is a third party service that easyly allows to track users behaviour on website and make statistics about gthese behaviours.

Tutorial : when we want to track a click or a view, we simply add data-track-click or data-track-view to appropriate html tags, along with some details (see example just bellow).

Example: setup track a reject cookies click on a cookie button

    <button data-track-click='{"key": "cookies-user-decision", "attributes": {"value": "reject"} }'>reject</button>

    Such a button will be tracked thanks to code bellow

*/

$(document).ready(function(){

    $("[data-track-click]").on("click", function(event){  //

        track = $(this).data("track-click");

        mixpanel.track(track.key, track.attributes);

    });

    $("[data-track-view]").each(function( index ) {

        track = $(this).data("track-view");

        mixpanel.track(track.key, track.attributes);

    });

});

// Mixpanel Tracking Code
(function(f,b){if(!b.__SV){var e,g,i,h;window.mixpanel=b;b._i=[];b.init=function(e,f,c){function g(a,d){var b=d.split(".");2==b.length&&(a=a[b[0]],d=b[1]);a[d]=function(){a.push([d].concat(Array.prototype.slice.call(arguments,0)))}}var a=b;"undefined"!==typeof c?a=b[c]=[]:c="mixpanel";a.people=a.people||[];a.toString=function(a){var d="mixpanel";"mixpanel"!==c&&(d+="."+c);a||(d+=" (stub)");return d};a.people.toString=function(){return a.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms track_with_groups add_group set_group remove_group register register_once alias unregister identify name_tag set_config reset opt_in_tracking opt_out_tracking has_opted_in_tracking has_opted_out_tracking clear_opt_in_out_tracking start_batch_senders people.set people.set_once people.unset people.increment people.append people.union people.track_charge people.clear_charges people.delete_user people.remove".split(" ");

for(h=0;h<i.length;h++)g(a,i[h]);var j="set set_once union unset remove delete".split(" ");a.get_group=function(){function b(c){d[c]=function(){call2_args=arguments;call2=[c].concat(Array.prototype.slice.call(call2_args,0));a.push([e,call2])}}for(var d={},e=["get_group"].concat(Array.prototype.slice.call(arguments,0)),c=0;c<j.length;c++)b(j[c]);return d};b._i.push([e,f,c])};b.__SV=1.2;e=f.createElement("script");e.type="text/javascript";e.async=!0;e.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===f.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";g=f.getElementsByTagName("script")[0];g.parentNode.insertBefore(e,g)}})(document,window.mixpanel||[]);

// Enabling the debug mode flag is useful during implementation,
// but it's recommended to remove it for production
mixpanel.init('04e019910114a2706779d88fba4c1044', {'debug':'true'});



//counting page viewed by user

var pageViews = localStorage.getItem('pageViews');

pageViews ? localStorage.setItem('pageViews', parseInt(pageViews)+1) : localStorage.setItem('pageViews', 1);

pageViews = localStorage.getItem('pageViews'); // refreshing value