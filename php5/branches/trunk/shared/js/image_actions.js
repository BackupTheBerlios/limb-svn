function change_action(sel, variation)
{
	obj = document.getElementById(variation + '_generate_div');
	if (obj)
	  obj.style.display = 'none';
		
	obj = document.getElementById(variation + '_upload_div');
	if (obj)
	  obj.style.display = 'none';
		
	obj = document.getElementById(variation + '_nothing_div');
	if (obj)
	  obj.style.display = 'none';
		
	obj = document.getElementById(variation + '_' + sel.value + '_div');
	if (obj)
	  obj.style.display = 'block';
}
