function get_cookie(name)
{
  var a_cookie = document.cookie.split("; ");
  for (var i=0; i < a_cookie.length; i++)
  {
    var a_crumb = a_cookie[i].split("=");
    if (name == a_crumb[0])
      return unescape(a_crumb[1]);
  }
  return null;
}

function set_cookie(name, value, path, expires)
{
  path_str = (path) ? '; path=' + path : '; path=/';
  expires_str = (expires) ? '; expires=' + expires : '';

  cookie_str = name + '=' + value + path_str + expires_str;

  document.cookie = cookie_str;
}

function remove_cookie(name, path)
{
  set_cookie(name, 0, path, '1/1/1980');
}

function add_cookie_element(cookie_name, element)
{
  cookie_elements = get_cookie(cookie_name);
  if (cookie_elements == null || cookie_elements == 'undefined')
    cookie_elements_array = new Array();
  else
    cookie_elements_array = cookie_elements.split(',');
  present = 0;
  for(i=0; i<cookie_elements_array.length; i++)
  {
    if (cookie_elements_array[i] == element)
    {
      present = 1;
      break;
    }
  }
  if (!present)
  {
    cookie_elements_array.push(element);
    new_cookie_elements = cookie_elements_array.join(',');
    set_cookie(cookie_name, new_cookie_elements);
  }
}

function remove_cookie_element(cookie_name, element)
{
  cookie_elements = get_cookie(cookie_name);
  if (cookie_elements == null || cookie_elements == 'undefined')
    cookie_elements_array = new Array();
  else
    cookie_elements_array = cookie_elements.split(',');
  new_cookie_elements_array = new Array();
  present = 0;
  for(i=0; i<cookie_elements_array.length; i++)
  {
    if (cookie_elements_array[i] != element)
      new_cookie_elements_array.push(cookie_elements_array[i]);
    else
      present = 1;
  }
  if (present)
  {
    new_cookie_elements = new_cookie_elements_array.join(',');
    set_cookie(cookie_name, new_cookie_elements);
  }
}


