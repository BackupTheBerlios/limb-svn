function image_select(id, img_id)
{
	this.id_container = document.getElementById(id);
	this.img = document.getElementById(img_id);
}

image_select.prototype.generate = function()
{
	if (this.id_container.value)
	{
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
	this.id_container.value = image.node_id
	
	this.generate();
}


/*
	function ic_insert_image(objname, image)
	{
		data = objname.split(':');
		prefix = '_' + data[0] + '_' + data[1];
		div_obj = document.getElementById(prefix + '_div');
		
	  if (!image.type)
	  	image.type = 'thumbnail';

	  img_element = document.createElement("IMG");
	  for(id in image)
	  {
	  	if ((id == 'width' || id == 'height') && (image[id] == 0 || image[id] == ''))
	  		continue;
	  	
		  img_element[id] = image[id];
	  	set_image_property(prefix, id, image[id]);
	  }
	  
	  img_element.src = '/image/' + image.image_id + '.' + image.type;

		value = parseInt(image.width);
	  if (value != 0 && !isNaN(value))
	  	img_element.width = value;
		
		value = parseInt(image.height);
	  if (value != 0 && !isNaN(value))
	  	img_element.height = value;
	  	
	  link_to = image.link_to;
	  if (link_to && link_to.length > 1)
	  {
		  link_element = document.createElement("A");
		  link_element.href = '/image/' + image.id + '.' + link_to;
		  link_element.target = '_blank';
		  link_element.appendChild(img_element);
		}
		else
			link_element = null;
		
		if (link_element != null)
			if (div_obj.firstChild == null)
				div_obj.appendChild(link_element);
			else
				div_obj.replaceChild(link_element, div_obj.firstChild);
		else
			if (div_obj.firstChild == null)
				div_obj.appendChild(img_element);
			else
				div_obj.replaceChild(img_element, div_obj.firstChild);
		
		optimize_window();
	}

	function ic_get_image(objname)
	{
		data = objname.split(':');
		prefix = '_' + data[0] + '_' + data[1];
		
		image_id = get_image_property(prefix, 'image_id');
		if (image_id)
		{
			img = {id: get_image_property(prefix, 'id'),
						 image_id: get_image_property(prefix, 'image_id'),
						 name: get_image_property(prefix, 'name'),
						 description: get_image_property(prefix, 'description'),
						 width: get_image_property(prefix, 'widht'),
						 height: get_image_property(prefix, 'height'),
						 border: get_image_property(prefix, 'border'),
						 hspace: get_image_property(prefix, 'hspace'),
						 vspace: get_image_property(prefix, 'vspace'),
						 align:	get_image_property(prefix, 'align'),
						 alt:	get_image_property(prefix, 'alt'),
						 type: get_image_property(prefix, 'type'),
						 link_to: get_image_property(prefix, 'link_to')};
			
			return img;
		}
		else
			return null;
	}
	
*/