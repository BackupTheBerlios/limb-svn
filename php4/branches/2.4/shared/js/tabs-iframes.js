var tabs = new tabs_container('top_tabs');

function activate_tab(tab)
{
 var current_path = window.location.pathname + window.location.search;
 if (current_path != tab.data.src)
   if (last_tab_url = getCookieWithId('TABs', tab.id + '_last_url'))
     window.location.href = last_tab_url;
   else
     window.location.href = tab.data.src;
}

function highlight_tab(container, tab_id)
{
  if(!container.tab_items[tab_id])
    return;
  container.tab_items[tab_id].label.className = container.active_tab_class_name;
  container.active_tab = container.tab_items[tab_id];
}

