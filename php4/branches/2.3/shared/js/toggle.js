function toggle_display(obj_id)
{
  obj = document.getElementById(obj_id);
  if (typeof(obj) == 'object')
    if (typeof(obj.length) != 'undefined')
      for(i=0; i<obj.length; i++)
        toggle_obj_display(obj[i]);
    else
      toggle_obj_display(obj);
}

function toggle_obj_display(obj)
{
  if (obj.style.display == 'none')
  {
    obj.style.display = 'block';
    add_cookie_element('displayed_objects', obj.id);
    remove_cookie_element('hidden_objects', obj.id);
  }
  else
  {
    obj.style.display = 'none';
    add_cookie_element('hidden_objects', obj.id);
    remove_cookie_element('displayed_objects', obj.id);
  }
}
