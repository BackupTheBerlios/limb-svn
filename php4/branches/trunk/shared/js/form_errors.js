function get_label_for_field(id)
{
  if(document.getElementsByTagName('label').length > 0)
  {
    labels = document.getElementsByTagName('label');
    for(c=0; c<labels.length; c++)
    {
      if(labels[c].htmlFor == id)
        return labels[c].firstChild.nodeValue;
    }
  }
  return null;
}

function set_error(id, msg)
{
  obj = document.getElementById(id);
  if(!obj)
    return;

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

  set_field_summary_error(id, msg);
}

function set_field_summary_error(id, msg)
{
  span = document.getElementById("error_summary_for_" + id);

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
  span.appendChild(document.createTextNode(" : " + msg));
}

function check_form_errors()
{
  //someday client validation will be here
  return true;
}