function set_error(id, msg)
{
  obj = document.getElementById(id);
  if (obj)
  {
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
}