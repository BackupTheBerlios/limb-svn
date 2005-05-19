function get_query_item(page_href, item_name)
{
  arr = get_query_items(page_href);

  if(arr[item_name])
    return arr[item_name];
  else
    return null;
}

function build_query(items)
{
  query = '';
  for(index in items)
  {
    query = query + index + '=' + items[index] + '&';
  }
  return query;
}

function get_query_items(uri)
{
  query_items = new Array();

  arr = uri.split('?');
  if(!arr[1])
    return query_items;

  query = arr[1];

  arr = query.split('&');

  for(index in arr)
  {
    if(arr[index])
    {
      key_value = arr[index].split('=');
      if(!key_value[1])
        continue;

      query_items[key_value[0]] = key_value[1];
    }
  }

  return query_items;
}

function set_http_get_parameter(uri, parameter, val)
{
  uri_pieces = uri.split('?');

  items = get_query_items(uri);
  items[parameter] = val;

  return uri_pieces[0] + '?' + build_query(items);
}

function add_random_to_url(page_href)
{
  if(page_href.indexOf('?') == -1)
    page_href = page_href + '?';

  page_href = page_href.replace(/&*rn=[^&]+/g, '');

  items = page_href.split('#');

  page_href = items[0] + '&rn=' + Math.floor(Math.random()*10000);

  if(items[1])
    page_href = page_href + '#' + items[1];

  return page_href;
}

//makes window w(current if not specified) go to href address
function jump(href, w)
{
  if(!w)
    w = window;

  w.location.href = LOADING_STATUS_PAGE;
  w.location.href = href;
}

//makes window w(current if not specified) reload itself with new get request
function jump_change_get(get, w)
{
  href = document.location.href;
  is_get = href.indexOf('?');

  if(is_get > -1)
    href = href.substring(0, '?')//get_begin);

  jump(href + '?' + get, w);
}

function click_href(href, window_name)
{
  is_popup = href.indexOf('popup=1');
  if(is_popup > -1)
    popup(href, window_name);

  return !(is_popup > -1);
}

function goto_page(message, href)
{
  if (confirm(message))
    jump(href)
}
