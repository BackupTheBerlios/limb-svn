function open_cart_window(href, cart_name)
{
	w = window.open(href, cart_name, 'directories=no,location=no,status=no,resizable=yes,scrollbars=yes,width=800');
	w.focus();
	return false;
}

function submit_to_cart(form, cart_name)
{
	get_string = '';
	for(i=0; i<form.elements.length;i++)
	{
		e = form.elements[i];
		if(e.type == 'text' && e.value)
		{
			get_string += '&' + escape(e.name) + '=' + escape(e.value);
			e.value = '';
		}
	}
	
	w_name = cart_name;
	w_name = w_name.replace(/\//g, "_");
	w_name = w_name.replace(/.*cart_offer/, "cart_offer");
	w_name = w_name.replace(/.*cart_demand/, "cart_demand");
	
	w = window.open(form.action + get_string, w_name,'directories=no,location=no,status=no,resizable=yes,scrollbars=yes,width=800');
	w.focus(w_name);
	
	return false;
}