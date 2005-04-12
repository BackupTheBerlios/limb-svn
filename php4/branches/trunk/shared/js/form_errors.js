function get_label_for_field(id)
{
  if(document.getElementsByTagName('label').length > 0)
  {
    labels = document.getElementsByTagName('label');
    for(c=0; c<labels.length; c++)
    {
      if(labels[c].htmlFor == id)
        return labels[c].innerHTML;
    }
  }
  return null;
}

function default_form_field_error_printer(id, msg)
{
  obj = document.getElementById(id);
  span = document.createElement('SPAN');
  br = document.createElement('BR');
  text = document.createTextNode(msg);
  span.appendChild(text);
  span.style.color = 'red';

  obj.parentNode.insertBefore(span, obj);
  obj.parentNode.insertBefore(br, obj);
  obj.style.borderColor = 'red';
  obj.style.borderStyle = 'solid';
  obj.style.borderWidth = '1px';
}

function default_form_field_error_label_printer(id, msg)
{
  span = document.getElementById("label_for_" + id);

  if(!span)
    return;

  label = get_label_for_field(id);

  if(!label)
    return;

  newa = document.createElement('a');
  newa.appendChild(document.createTextNode(label));
  newa.href = '#'+id;
  newa.isid = id;
  newa.onclick = function()
  {
    document.getElementById(this.isid).focus();
    return false;
  }

  span.appendChild(newa);
}

function set_form_field_error(id, msg)
{
  obj = document.getElementById(id);
  if(!obj)
    return;

  if(typeof(form_field_error_printer) == "function")
    form_field_error_printer(id, msg);
  else
    default_form_field_error_printer(id, msg);

  if(typeof(form_field_error_label_printer) == "function")
    form_field_error_label_printer(id, msg);
  else
    default_form_field_error_label_printer(id, msg);
}

function check_form_errors()
{
  //someday client validation will be here
  return true;
}