function toggle_submenu(index)
{
	sb = document.getElementById("sb"+index);
	submenu = document.getElementById("submenu"+index);
	
	if (sb.style.display == 'none')
	{
		submenu.src = '/shared/images/right_arrow.gif';
		sb.style.display = 'block';
		opened_submenu = get_cookie('opened_submenu');
		if (opened_submenu == null || opened_submenu == 'undefined') 
			new_opened_submenu_array = new Array();
		else
			new_opened_submenu_array = opened_submenu.split(',');
		new_opened_submenu_array.push(index);
		new_opened_submenu_array.sort();
	}
	else
	{
		submenu.src = '/shared/images/down_arrow.gif';
		sb.style.display = 'none';
		opened_submenu = get_cookie('opened_submenu');
		opened_submenu_array = opened_submenu.split(',');
		new_opened_submenu_array = new Array();
	  for (var i=0; i < opened_submenu_array.length; i++)
	  	if (opened_submenu_array[i] != index)
	  		new_opened_submenu_array.push(opened_submenu_array[i]);
	}
	new_opened_submenu = new_opened_submenu_array.join(',');
	set_cookie('opened_submenu', new_opened_submenu)
}

