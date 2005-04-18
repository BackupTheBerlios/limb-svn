function add_page_to_favourities()
{
  if (window.sidebar)
    window.sidebar.addPanel(window.document.title, window.location, "");
  else if(document.all)
    window.external.AddFavorite( window.location, window.document.title);
  else if(window.opera && window.print)
    return true;
}

function make_homepage(obj)
{
  if(document.all)
  {
    obj.event.srcElement.style.behavior='url(#default#homepage)';
    obj.event.srcElement.setHomePage(window.location);
  }
}
