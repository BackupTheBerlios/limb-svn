	function store_tab_url()
	{
		wname = window.name;
		tab_name = 'tab_' + wname.substr(7);
		set_cookie(tab_name + '_last_url', escape(window.location.href));
	}
	
	function refresh_location_bar()
	{
		if(typeof(top.refresh_url) == 'function')
		  top.refresh_url();
	}
  
	add_event(window, 'unload', store_tab_url)
	add_event(window, 'load', refresh_location_bar)