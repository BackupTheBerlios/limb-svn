function image_select(id, md5_id)
{
	this.id_container = document.getElementById(id);
	this.img = document.getElementById(md5_id + '_img');
	this.name = document.getElementById(md5_id + '_name');
}

image_select.prototype.generate = function()
{
	if (this.id_container.value)
	{
		if(this.id_container.value == 0)
			this.img.src = '/shared/images/no_img.gif';
		else
	  	this.img.src = '/root?node_id=' + this.id_container.value;
		
//		optimize_window();
	}
}

image_select.prototype.get_image = function()
{
  if(this.id_container.value == 0)
    return null;
  
	img = {
		node_id: this.id_container.value, 
		name: this.name.innerHTML,
		start_path: this.start_path
		};
	
	return img;
}

image_select.prototype.insert_image = function(image)
{
	this.id_container.value = image.node_id;
	this.name.innerHTML = image.name;
	
	this.generate();
}

image_select.prototype.set_start_path = function(start_path)
{
	this.start_path = start_path;
}