	function store_tab_url()
	{
		wname = window.name;
		tab_name = 'tab_' + wname.substr(7);
		set_cookie(tab_name + '_last_url', escape(window.location.href));
	}
	
	add_event(window, 'unload', store_tab_url)