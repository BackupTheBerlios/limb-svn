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
  this.drawOptions();
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
  var table = this.addElement('table', parent);
  table = this.addElement('tr', table);

  td = this.addElement('td', table);
  this.srcSelect = this.addElement('select', td);
  this.srcSelect.multiple = true;
  this.srcSelect.size = '10';
  this.srcSelect.style.width = '150px';

  td = this.addElement('td', table);
  button = this.addElement('input', td);
  button.type = 'button';
  button.value = '>>';
  button.onclick = this.selectAll;
  button.selector_obj = this;

  this.addElement('br', td);
  this.addElement('br', td);
  button = this.addElement('input', td);
  button.type = 'button';
  button.value = ' > ';
  button.onclick = this.selectItems;
  button.selector_obj = this;

  this.addElement('br', td);
  this.addElement('br', td);
  button = this.addElement('input', td);
  button.type = 'button';
  button.value = ' < ';
  button.onclick = this.deselectItems;
  button.selector_obj = this;

  this.addElement('br', td);
  this.addElement('br', td);
  button = this.addElement('input', td);
  button.type = 'button';
  button.value = '<<';
  button.onclick = this.deselectAll;
  button.selector_obj = this;

  td = this.addElement('td', table);
  this.dstSelect = this.addElement('select', td);
  this.dstSelect.multiple = true;
  this.dstSelect.size = '10';
  this.dstSelect.style.width = '150px';
}

DoubleSelect.prototype.addElement = function(type, parent)
{
  element = document.createElement(type);
  parent.appendChild(element);
  return element;
}

DoubleSelect.prototype._moveOptions = function(id, remove, only_selected)
{
  var form = src.form;
  var before = null;

  for(i = 0; i < src.options.length;)
  {
    if(src.options[i].selected || !only_selected)
    {
      option = src.options[i];
      before = null;
      for(j = 0; j < dst.options.length; j++)
      {
        if(dst.options[j].text > option.text)
        {
           before = dst.options[j];
           break;
        }
      }
      option.selected = false;

      if(dst.options.length == 0 || !before)
        dst.appendChild(option);
      else
        dst.insertBefore(option, before);

    }
    else
      i++;
  }
}

DoubleSelect.prototype.selectAll = function()
{
  var selector = this.selector_obj;
  for(i = 0; i < selector.select.options.length; i++)
    selector.select.options[i].selected = true;
  selector.drawOptions();
}

DoubleSelect.prototype.deselectAll = function()
{
  var selector = this.selector_obj;
  for(i = 0; i < selector.select.options.length; i++)
    selector.select.options[i].selected = false;
  selector.drawOptions();
}

DoubleSelect.prototype.selectItems = function()
{
  this.selector_obj.setSelection(this.selector_obj.srcSelect, this.selector_obj.select, true);
  this.selector_obj.drawOptions();
}

DoubleSelect.prototype.deselectItems = function()
{
  this.selector_obj.setSelection(this.selector_obj.dstSelect, this.selector_obj.select, false);
  this.selector_obj.drawOptions();
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

