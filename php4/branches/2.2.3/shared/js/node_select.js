function node_select(id, md5_id, start_path)
{	
	this.id_container = document.getElementById(id);
	this.identifier = document.getElementById(md5_id + '_identifier');
	this.title = document.getElementById(md5_id + '_title');
	this.icon = document.getElementById(md5_id + '_icon');
	this.class_name = document.getElementById(md5_id + '_class_name');
	this.path = document.getElementById(md5_id + '_path');
	this.parent_node_id = document.getElementById(md5_id + '_parent_node_id');
}

node_select.prototype.generate = function()
{			
//	optimize_window();
}

node_select.prototype.set_start_path = function(start_path)
{
	this.start_path = start_path;
}

node_select.prototype.set_only_parents_restriction = function(flag)
{
	this.only_parents = flag;
}


node_select.prototype.get_node = function()
{
	node = {
		node_id: this.id_container.value,
		parent_node_id : this.parent_node_id.innerHTML,
		identifier: this.identifier.innerHTML,
		title: this.title.innerHTML,
		icon: this.icon.src,
		path: this.path.innerHTML,
		class_name: this.class_name.innerHTML,
		start_path: this.start_path,
		only_parents: this.only_parents
	};
	
	return node;
}

node_select.prototype.insert_node = function(node)
{
	this.id_container.value = node.node_id;
	this.identifier.innerHTML = node.identifier;
	this.title.innerHTML = node.title;
	this.path.innerHTML = node.path;
	this.icon.src = node.icon;
	this.parent_node_id.innerHTML = node.parent_node_id;
	this.class_name.innerHTML = node.class_name;
	
	this.generate();
}

node_select.prototype.reset = function()
{
	this.id_container.value = 0;
	this.identifier.innerHTML = '';
	this.title.innerHTML = '';
	this.path.innerHTML = '';
	this.icon.src = '/shared/images/no.gif';
	this.parent_node_id.innerHTML = '';
	this.class_name.innerHTML = '';
}
