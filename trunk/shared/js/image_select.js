function image_select(id, img_id)
{
	this.id_container = document.getElementById(id);
	this.img = document.getElementById(img_id);
}

image_select.prototype.generate = function()
{
	if (this.id_container.value)
	{
		if(this.id_container.value == 0)
			this.img.src = '/shared/images/no_img.gif';
		else
	  	this.img.src = '/root?node_id=' + this.id_container.value;
		
		optimize_window();
	}
}

image_select.prototype.get_image = function()
{
	img = {node_id: this.id_container.value};
	
	return img;
}

image_select.prototype.insert_image = function(image)
{
	this.id_container.value = image.node_id;
	
	this.generate();
}