var LOADING_STATUS_PAGE = '/shared/loading.html';
var INCLUDED_SCRIPTS = new Array();

function include(src)
{
  if(typeof(INCLUDED_SCRIPTS[src]) != 'undefined')
    return;

  script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = src;
  document.getElementsByTagName('head')[0].appendChild(script);

  INCLUDED_SCRIPTS[src] = true;
}

include('/shared/js/browser.js');
include('/shared/js/http.js');
include('/shared/js/window.js');
include('/shared/js/events.js');
include('/shared/js/forms.js');
include('/shared/js/cookie.js');
include('/shared/js/security.js');
include('/shared/js/string.js');
include('/shared/js/dom.js');
include('/shared/js/favourites.js');//remove later!
include('/shared/js/ims.js');//remove later!

function post_load_handler()
{
  if(get_query_item(location.href, 'popup'))
    process_popup();
}

//we can't use nice add event here, because it may be not loaded yet
prev_window_on_load_handler = window.onload;
window.onload = function()
{
  if(typeof(prev_window_on_load_handler) == 'function')
    prev_window_on_load_handler();

  post_load_handler();
}

function isset(obj)
{
  return typeof(obj) != 'undefined' && obj != null
}

