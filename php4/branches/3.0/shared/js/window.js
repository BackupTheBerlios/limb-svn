include('/shared/js/md5.js');
include('/shared/js/http.js');

var POPUP_STATUS_OPENED         = 1;
var POPUP_STATUS_PROCESSED      = 2;

function get_close_popup_handler()
{
  return opener.popups[window.name]['close_popup_handler'];
}

function get_init_popup_handler()
{
  return opener.popups[window.name]['init_popup_handler'];
}

function optimize_window()
{
  var w = window;
  var top_opener = window;

  var x_ratio = 0.85;
  var y_ratio = 0.85;
  var screen_x = (is_gecko) ? top_opener.screenX : top_opener.screenLeft;

  while(typeof(top_opener.top.opener) != 'undefined' && top_opener.top.opener != null && screen_x > 0)
  {
    screen_x = (is_gecko) ? top_opener.screenX : top_opener.screenLeft;
    top_opener = top_opener.top.opener;
  }

  if (is_ie)
  {
    openerWidth = top_opener.document.body.clientWidth;
    openerHeight = top_opener.document.body.clientHeight;
    openerLeft = top_opener.screenLeft;
    openerTop = top_opener.screenTop;
  }
  else if(is_gecko || is_opera)
  {
    openerWidth = top_opener.innerWidth;
    openerHeight = top_opener.innerHeight;
    openerLeft = top_opener.screenX + top_opener.outerWidth - top_opener.innerWidth;
    openerTop = top_opener.screenY + top_opener.outerHeight - top_opener.innerHeight;
  }
  else
  {
    openerWidth = top_opener.document.body.clientWidth;
    openerHeight = top_opener.document.body.clientHeight;
    openerLeft = top_opener.screenLeft;
    openerTop = top_opener.screenTop;
  }

  if(window.WINDOW_WIDTH)
    newWidth = window.WINDOW_WIDTH;
  else
    newWidth = openerWidth*x_ratio;

  if(window.WINDOW_HEIGHT)
    newHeight = window.WINDOW_HEIGHT;
  else
    newHeight = openerHeight*y_ratio;


  newLeft = openerLeft + (openerWidth - newWidth)/2;
  newTop = openerTop + (openerHeight - newHeight)/2;

  w.moveTo(newLeft, newTop);
  w.resizeTo(newWidth, newHeight);
}

//makes popup window at href address
function popup(href, window_name, window_params, dont_set_focus, on_close_handler, on_init_handler)
{
  href = set_http_get_parameter(href, 'popup', 1);

  if (typeof(window_name) == 'undefined' || window_name == null)
    window_name = '_generate';

  new_left = window.screen.width / 2 - 100;
  new_top = window.screen.height / 2 - 50;

  if (typeof(window_params) == 'undefined' || window_params == null)
    window_params = 'width=150,height=50,left=' + new_left + ',top=' + new_top + ',scrollbars=yes,resizable=yes,help=no,status=yes';
  else
    window_params = 'left=' + new_left + ',top=' + new_top + ',' + window_params;

  if (window_name.toLowerCase() == '_generate')
    window_name = 'w' + hex_md5(href) + 's';

  if (typeof(window.popups) != 'array')
    window.popups = new Array();

  if (typeof(window.popups[window_name]) != 'array')
    window.popups[window_name] = new Array();

  if (typeof(on_close_handler) != 'undefined')
    window.popups[window_name]['close_popup_handler'] = on_close_handler;

  if (typeof(on_init_handler) != 'undefined')
    window.popups[window_name]['init_popup_handler'] = on_init_handler;

  window.popups[window_name]['status'] = POPUP_STATUS_OPENED;

  w = window.open(href, window_name, window_params);

  if(!dont_set_focus)
    w.focus();

  return w;
}

function process_popup()
{
  href = location.href;

  if (window.opener && window.opener.popups)
    if (window.opener.popups[window.name]['status'] == POPUP_STATUS_OPENED)
      optimize_window();

  if(opener && (get_query_item(href, 'reload_parent')))
    opener.location.reload();
  else
    if (typeof(window.opener.popups) != 'undefined')
      window.opener.popups[window.name]['status'] = POPUP_STATUS_PROCESSED;
}

function open_page(message, href, window_name, window_params)
{
  if (typeof(window_name) == 'undefined' || window_name == null)
    window_name = '_generate';

  if (typeof(window_params) == 'undefined' || window_params == null)
    window_name = 'height=400,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes';

  if (confirm(message))
    popup(href, window_name, window_params)
}
