function file_select(id, img_id, name_id, description_id)
{
	this.id_container = document.getElementById(id);
	this.img = document.getElementById(img_id);
	this.name = document.getElementById(name_id);
	this.description = document.getElementById(description_id);
}

file_select.prototype.generate = function()
{
	if(this.id_container.value != 0)
	{
		this.img.src = '/root?node_id=' + this.id_container.value + '&icon';
	}
	else
		this.img.src = '/shared/images/no_img.gif';
			
	optimize_window();
}

file_select.prototype.get_file = function()
{
	file = {node_id: this.id_container.value, name: this.name.innerHTML, description: this.description.innerHTML};
	return file;
}

file_select.prototype.insert_file = function(file)
{
	this.id_container.value = file.node_id;
	this.name.innerHTML = file.name;
	this.description.innerHTML = file.description;
	
	this.generate();
}