//function tab(container, id, active_tab_class_name, normal_tab_class_name, onmousedown_handler, activate_handler, deactivate_handler)
function tab(container, tab_data)
{
  this.data = tab_data;
  this.container = container;
  this.id = tab_data.id;
  this.label = document.getElementById(tab_data.id);
  this.label.tab = this;
  this.content = document.getElementById(tab_data.id + '_content');

  this.prev_height = this.content.style.height;
  this.prev_width = this.content.style.width;
  this.prev_css = this.content.cssText;

  if(typeof(tab_data.active_tab_class_name) == undefined || tab_data.active_tab_class_name == null)
    tab_data.active_tab_class_name = 'tab-active';

  if(typeof(tab_data.normal_tab_class_name) == undefined || tab_data.normal_tab_class_name == null)
    tab_data.normal_tab_class_name = 'tab';

  this.active_tab_class_name = tab_data.active_tab_class_name;
  this.normal_tab_class_name = tab_data.normal_tab_class_name;

  this.onmousedown_handler = tab_data.onmousedown_handler;
  this.activate_handler = tab_data.activate_handler;
  this.deactivate_handler = tab_data.deactivate_handler;

  this.label.onmousedown = function()
  {
    if(!this.tab.container.active_tab)
      return;

    this.tab.activate();

    if(this.tab.onmousedown)
      this.tab.onmousedown();
  }
}

tab.is_gecko = (navigator.product == "Gecko");

tab.prototype.activate = function()
{
  if(this.container.active_tab == this)
    return;

  if(this.container.active_tab)
    this.container.active_tab.deactivate();

  this.container.active_tab = this;

  if(tab.is_gecko)
  {
    if(this.content.style.visibility == 'hidden')
      this.content.style.visibility = 'visible';

    this.content.style.cssText  = this.prev_css;
    if(this.prev_height && this.prev_width)
    {
      this.content.style.height = this.prev_height;
      this.content.style.width = this.prev_width;
    }
  }
  else
    this.content.style.display = 'block';


  this.label.className = this.active_tab_class_name;

  set_cookie('active_tab', this.id)

  if(this.activate_handler)
    this.activate_handler(this);
}

tab.prototype.deactivate = function()
{
  if(tab.is_gecko)
  {
    this.prev_height = this.content.style.height;
    this.prev_width = this.content.style.width;
    this.prev_css = this.content.style.cssText;

    this.content.style.cssText = '';
    this.content.style.height = 0;
    this.content.style.width = 0;
    this.content.style.visibility = 'hidden';
  }
  else
    this.content.style.display = 'none';

  this.label.className = this.normal_tab_class_name;

  if(this.deactivate_handler)
    this.deactivate_handler(this);
}

function tabs_container()
{
   this.tab_items = new Array();
   this.active_tab = null;
}

//tabs_container.prototype.register_tab_item = function(id, active_tab_class_name, normal_tab_class_name, onmousedown_handler, activate_handler, deactivate_handler)
tabs_container.prototype.register_tab_item = function(tab_data)
{
   this.normal_tab_class_name = tab_data.normal_tab_class_name;
   this.tab_items[tab_data.id] = new tab(this, tab_data);
}

tabs_container.prototype.activate = function(tab_id)
{
  var active_tab_id, first_tab_id;

  for(var id in this.tab_items)
  {
    if(typeof(this.tab_items[id]) != 'object')
      continue;
    if (!first_tab_id)
      first_tab_id = id;

    if(id == tab_id)
    {
      this.tab_items[id].activate();
      active_tab_id = tab_id;
    }
    else
      this.tab_items[id].deactivate();
  }

  if (!active_tab_id)
    this.tab_items[first_tab_id].activate();
}