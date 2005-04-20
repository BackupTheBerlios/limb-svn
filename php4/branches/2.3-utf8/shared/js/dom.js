function containsDOM (container, containee)
{
  var isParent = false;
  do {
    if ((isParent = container == containee))
      break;
    containee = containee.parentNode;
  }
  while (containee != null);
  return isParent;
}

//shouldn't be here???
function checkMouseEnter (element, evt)
{
  if (element.contains && evt.fromElement)
    return !element.contains(evt.fromElement);
  else if (evt.relatedTarget)
    return !containsDOM(element, evt.relatedTarget);
}

//shouldn't be here???
function checkMouseLeave (element, evt)
{
  if (element.contains && evt.toElement)
    return !element.contains(evt.toElement);
  else if (evt.relatedTarget)
    return !containsDOM(element, evt.relatedTarget);
}
