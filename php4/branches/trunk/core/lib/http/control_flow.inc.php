<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

define('RELOAD_SELF_URL', '');

function add_url_query_items($url, $items=array())
{
  $str_params = '';

  $request = request :: instance();

  if (($node_id = $request->get_attribute('node_id')) && !isset($items['node_id']))
    $items['node_id'] = $node_id;

  if(strpos($url, '?') === false)
    $url .= '?';

  foreach($items as $key => $val)
  {
    $url = preg_replace("/&*{$key}=[^&]*/", '', $url);
    $str_params .= "&$key=$val";
  }

  $items = explode('#', $url);

  $url = $items[0];
  $fragment = isset($items[1]) ? '#' . $items[1] : '';

  return $url . $str_params . $fragment;
}

function close_popup_no_parent_reload_response()
{
  return "<html><body><script>
          if(window.opener)
          {
              window.opener.focus();
              window.close()
          };
        </script></body></html>";
}

function close_popup_response(&$request, $parent_reload_url = RELOAD_SELF_URL, $search_for_node = false)
{
  $str = "<html><body><script>
              if(window.opener)
              {";

  if($parent_reload_url != RELOAD_SELF_URL)
    $str .=			"	href = '{$parent_reload_url}';";
  else
    $str .=			"	href = window.opener.location.href;";

  if($search_for_node && !$request->has_attribute('recursive_search_for_node'))
    $str .=			_add_js_param_to_url('href', 'recursive_search_for_node', '1');

  $str .=				_add_js_random_to_url('href');

  $str .=				"	window.opener.location.href = href;";

  $str .=				" window.opener.focus();
                }
                window.close();
              </script></body></html>";

  return $str;

}

function _add_js_random_to_url($href)
{
  return _add_js_param_to_url($href, 'rn', 'Math.floor(Math.random()*10000)');
}

function _add_js_param_to_url($href, $param, $value)
{
  return "
    if({$href}.indexOf('?') == -1)
      {$href} = {$href} + '?';

    {$href} = {$href}.replace(/&*rn=[^&]+/g, '');

    items = {$href}.split('#');

    {$href} = items[0] + '&{$param}=' + {$value};

    if(items[1])
      {$href} = {$href} + '#' + items[1];";

}

function redirect_popup_response($url = '')
{
  if(!$url)
    $url = $_SERVER['PHP_SELF'];


  $str = "<html><body><script>
              if(window.opener)
              {
                href = window.opener.location.href;";

  $str .=				_add_js_random_to_url('href');

  $str .=				" window.opener.location.href = href;";

  $str .=			"
                window.opener.focus();
              }";

  $str .= "href = '{$url}';";

  $str .= _add_js_random_to_url('href');

  $str .= "window.location.href = href;";

  $str .= '</script>
          </body>
        </html>';

  return $str;
}

?>
