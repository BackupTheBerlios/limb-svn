function file_select(id, md5_id)
{	
	this.span_empty = document.getElementById(md5_id + '_span_empty');
	this.span_content = document.getElementById(md5_id + '_span_content');

	this.id_container = document.getElementById(id);
	this.img = document.getElementById(md5_id + '_img');
	this.a =  document.getElementById(md5_id + '_href');
	this.name = document.getElementById(md5_id + '_name');
	this.description = document.getElementById(md5_id + '_description');
	this.size = document.getElementById(md5_id + '_size');
	this.mime = document.getElementById(md5_id + '_mime');
}

file_select.prototype.generate = function()
{
	if(this.id_container.value != 0)
	{
		this.img.src = '/root?node_id=' + this.id_container.value + '&icon';
		
		this.a.href = '/root?node_id=' + this.id_container.value;
		
		this.span_empty.style.display = 'none';
		this.span_content.style.display = 'inline';
	}
	else
	{
		this.span_empty.style.display = 'inline';
		this.span_content.style.display = 'none';
	}
			
//	optimize_window();
}

file_select.prototype.get_file = function()
{
  if(this.id_container.value == 0)
    return null;
  
	file = {
					node_id: this.id_container.value, 
					name: this.name.innerHTML, 
					description: this.description.innerHTML,
					size: this.size.innerHTML,
					mime_type: this.mime.innerHTML
				};
	
	return file;
}

file_select.prototype.insert_file = function(file)
{
	this.id_container.value = file.node_id;
	this.name.innerHTML = file.name;
	this.description.innerHTML = file.description;	
	this.size.innerHTML = file.size;
	this.mime.innerHTML = file.mime_type;
	
	this.generate();
}