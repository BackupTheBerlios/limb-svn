// DoubleSelect Class
var DoubleSelect = function(instanceName)
{
  // Properties
  this.instanceName	= instanceName;
  this.select = null;
  this.srcSelect = null;
  this.dstSelect = null;
}

DoubleSelect.prototype.replaceSelect = function()
{
  this.select = document.getElementsByName(this.instanceName)[0];
  this.drawControl();
  this.drawOptions()
}

DoubleSelect.prototype.drawOptions = function()
{
  this.dstSelect.options.length = 0;
  this.srcSelect.options.length = 0;

  for(i = 0; i < this.select.options.length; i++)
  {
    if(this.select.options[i].selected)
      option = this.addElement('option', this.dstSelect);
    else
      option = this.addElement('option', this.srcSelect);

    option.value = this.select.options[i].value;
    option.text = this.select.options[i].text;
    option.selected = false;
  }
}

DoubleSelect.prototype.drawControl = function()
{
  this.select.style.display = 'none';
  var parent = this.select.parentNode;
  var div = this.addElement('div', parent);

  div.innerHTML = "<table><tr><td></td><td align='center' valign='middle'></td><td></td></tr></table>";
  container = div.childNodes[0].childNodes[0].childNodes[0];
  this.srcSelect = this.addSelector(container.childNodes[0])
  this.addButtons(container.childNodes[1]);
  this.dstSelect = this.addSelector(container.childNodes[2])
}

DoubleSelect.prototype.addSelector = function(parent)
{
  parent.innerHTML = "<select multiple style='width: 150px;' size='10'></select>"
  return parent.firstChild;
}

DoubleSelect.prototype.addButtons = function(parent)
{
  button = this.addElement('button', parent);
  button.innerHTML = '&gt;&gt;';
  button.style.display = 'inline';
  button.onclick = this.selectAll;
  button.selector_obj = this;

  this.addElement('br', parent);
  this.addElement('br', parent);
  button = this.addElement('button', parent);
  button.innerHTML = '&nbsp;&gt;&nbsp;';
  button.style.display = 'inline';
  button.onclick = this.selectItems;
  button.selector_obj = this;

  this.addElement('br', parent);
  this.addElement('br', parent);
  button = this.addElement('button', parent);
  button.innerHTML = '&nbsp;&lt;&nbsp;';
  button.style.display = 'inline';
  button.onclick = this.deselectItems;
  button.selector_obj = this;

  this.addElement('br', parent);
  this.addElement('br', parent);
  button = this.addElement('button', parent);
  button.innerHTML = '&lt;&lt;';
  button.style.display = 'inline';
  button.onclick = this.deselectAll;
  button.selector_obj = this;
}

DoubleSelect.prototype.addElement = function(type, parent)
{
  element = document.createElement(type);
  parent.appendChild(element);
  return element;
}

DoubleSelect.prototype.selectAll = function()
{
  var selector = this.selector_obj;
  for(i = 0; i < selector.select.options.length; i++)
    selector.select.options[i].selected = true;
  selector.drawOptions();
  return false;
}

DoubleSelect.prototype.deselectAll = function()
{
  var selector = this.selector_obj;
  for(i = 0; i < selector.select.options.length; i++)
    selector.select.options[i].selected = false;
  selector.drawOptions();
  return false;
}

DoubleSelect.prototype.selectItems = function()
{
  this.selector_obj.setSelection(this.selector_obj.srcSelect, this.selector_obj.select, true);
  this.selector_obj.drawOptions();
  return false;
}

DoubleSelect.prototype.deselectItems = function()
{
  this.selector_obj.setSelection(this.selector_obj.dstSelect, this.selector_obj.select, false);
  this.selector_obj.drawOptions();
  return false;
}

DoubleSelect.prototype.setSelection = function(source, main, selected)
{
  for(i = 0; i < source.options.length; i++)
  {
    if(!source.options[i].selected)
      continue;
    for(j = 0; j < main.options.length; j++)
    {
      if(main.options[j].value == source.options[i].value &&
         main.options[j].text == source.options[i].text)
      {
        main.options[j].selected = selected;
        continue;
      }
    }
  }
}

