opened_image_src = '/shared/images/marker/minus.gif';
closed_image_src = '/shared/images/marker/plus.gif';

tmpImage = new Image();
tmpImage.src = opened_image_src;
tmpImage.src = closed_image_src;

function toggle_submenu(id)
{
  if(typeof(document.getElementById(id)) == 'undefined' || document.getElementById(id) == null)
    return;

  if(document.getElementById(id).style.display == 'block')
  {
    document.getElementById(id).style.display = 'none';
    document.getElementById('img_'+id).src = closed_image_src;
    add_cookie_element('hidden_menus', id);
  }
  else
  {
    document.getElementById(id).style.display = 'block';
    document.getElementById('img_'+id).src = opened_image_src;
    remove_cookie_element('hidden_menus', id);
  }
}

function init_menu()
{
  hidden_menus = get_cookie('hidden_menus');
  if (hidden_menus == null || hidden_menus == 'undefined')
    return false;

  hidden_menus_array = hidden_menus.split(',');
  for(i=0; i < hidden_menus_array.length; i++)
  {
     if(hidden_menus_array[i] != '')
      toggle_submenu(hidden_menus_array[i]);
  }
}