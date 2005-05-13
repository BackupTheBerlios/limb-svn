var tabs = new tabs_container('top_tabs');

function get_frame(tab_id)
{
 var iframe_name = tabs.tab_items[tab_id].data.iframe_name;
 for(i=0; i<window.frames.length; i++)
   if (window.frames[i].name == iframe_name)
     return window.frames[i]
}

function activate_tab(tab)
{
 var frm = get_frame(tab.id)
 if (frm.location.href == 'about:blank' || frm.location.href == '')
   if (last_tab_url = getCookieWithId('TABs', tab.id + '_last_url'))
     frm.location.href = last_tab_url;
   else
     frm.location.href = tab.data.src;
 refresh_url();
}

function go_home(tab_id)
{
 var home_src = tabs.tab_items[tab_id].data.src;
 var frm = get_frame(tab_id)
 
 if (frm.location.href != home_src)
   frm.location.href = home_src;

 tabs.tab_items[tab_id].activate()
}

function reload(tab_id)
{
	get_frame(tab_id).location.reload();
 tabs.tab_items[tab_id].activate()
}

function refresh_url()
{
 var frm = get_frame(tabs.active_tab.id)
 if(location_bar = document.getElementById('active_iframe_url'))
  location_bar.value = frm.location;
}

function goto_address()
{
 href = '';
 if(location_bar = document.getElementById('active_iframe_url'))
  href = location_bar.value;

 var frm = get_frame(tabs.active_tab.id)
 frm.location.href = href;
}
