<tmpl:script>
<script>
	function set_image_property(prefix, prop_name, value)
	{
		obj = document.getElementById(prefix + '_' + prop_name);
		if (typeof(obj) == 'object' && obj != null)
			obj.value = value;
	}
	
	
	function get_image_property(prefix, prop_name)
	{
		obj = document.getElementById(prefix + '_' + prop_name);
		if (typeof(obj) == 'object' && obj != null)
			return obj.value;
		return null;
	}
	
	
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
</script>
</tmpl:script>
<table border=0>
<tr>
	<td><div id='<!--<<div_id>>-->'><!--<<image>>--></div></td>
	<td>
		<tmpl:image_property>
			<input type=hidden id='<!--<<property_id>>-->' name='<!--<<property_name>>-->' value='<!--<<property_value>>-->'>
		</tmpl:image_property>
		<input type='button' class='button' value='<!--<<button_caption>>-->' onclick='javascript:popup("<!--<<button_jump_url>>-->")'
	</td>
</tr>
</table>