function open_cart_window(href, cart_name)
{
	w = window.open(href, cart_name, 'directories=no,location=no,status=no,resizable=yes,scrollbars=yes,width=800');
	w.focus();
	return false;
}

function submit_to_cart(form, w_name)
{
	get_string = '';
	for(i=0; i<form.elements.length;i++)
	{
		e = form.elements[i];
		
		if((e.type == 'text' || e.type == 'hidden') && e.value)
		{
			get_string += '&' + escape(e.name) + '=' + escape(e.value);
			e.value = '';
		}
	}
	
	get_string += '&' + form.name + '[submitted]=1';
	
	w = window.open(form.action + get_string, w_name,'directories=no,location=no,status=no,resizable=yes,scrollbars=yes,width=100,height=100');
	w.focus(w_name);
	
	return false;
}