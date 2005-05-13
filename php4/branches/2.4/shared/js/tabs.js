//function tab(container, id, active_tab_class_name, normal_tab_class_name, onmousedown_handler, activate_handler, deactivate_handler)
function tab(container, tab_data)
{
  this.data = tab_data;
  this.container = container;
  this.id = tab_data.id;
  this.label = document.getElementById(tab_data.id);
  this.label.style.cursor = 'pointer'
  this.label.tab = this;
  this.content = document.getElementById(tab_data.id + '_content');

  this.prev_height = this.content.style.height;
  this.prev_width = this.content.style.width;
  this.prev_css = this.content.cssText;

  this.onmousedown_handler = tab_data.onmousedown_handler;
  this.activate_handler = tab_data.activate_handler;
  this.deactivate_handler = tab_data.deactivate_handler;

	var hit_obj = get_obj_by_id(this.label.getElementsByTagName('*') , "tab-label");
	if(!isset(hit_obj)) hit_obj = this.label
	hit_obj.tab = this
//  this.label.onmouseup = function()
  hit_obj.onmouseup = function()
  {
    if(!this.tab.container.active_tab)
      return;
	
    this.tab.activate();

    if(this.tab.onmouseup)
      this.tab.onmouseup();
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

    this.content.style.display = 'block';

  this.label.className = this.container.active_tab_class_name;

  setCookieWithId('TABs', this.container.id + 'active_tab', this.id)

  if(this.activate_handler)
    this.activate_handler(this);
}

tab.prototype.deactivate = function()
{
	this.content.style.display = 'none';
	
	this.label.className = this.container.normal_tab_class_name;
	
	if(this.deactivate_handler)
		this.deactivate_handler(this);
}

function tabs_container(id, tab_data)
{
   this.tab_items = []
   this.active_tab = null;
   this.id = id

	if(!tab_data) tab_data = []
	if(typeof(tab_data.active_tab_class_name) == undefined || tab_data.active_tab_class_name == null)
		tab_data.active_tab_class_name = 'tab-active';
	
	if(typeof(tab_data.normal_tab_class_name) == undefined || tab_data.normal_tab_class_name == null)
		tab_data.normal_tab_class_name = 'tab';
	
	this.active_tab_class_name = tab_data.active_tab_class_name;
	this.normal_tab_class_name = tab_data.normal_tab_class_name;
}

//tabs_container.prototype.register_tab_item = function(id, active_tab_class_name, normal_tab_class_name, onmousedown_handler, activate_handler, deactivate_handler)
tabs_container.prototype.register_tab_item = function(tab_data)
{
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

tabs_container.prototype.activate_default = function()
{
	var id = getCookieWithId('TABs', this.id + 'active_tab')
	if (id) this.activate(id);
	else this.activate('');
}