dom = (document.getElementById) ? true : false;
nn4 = (document.hints) ? true : false;
ie = (document.all) ? true : false;
ie4 = ie && !dom;

var current_hint_x = -1, current_hint_y = -1;
var current_hint_id = null;
var enable_hint_moving = true;
//document.onmousemove = hint_mouse_move_handler; 
add_event(document, 'mousemove', hint_mouse_move_handler);

function hint_mouse_move_handler(e)
{
  if (dom)
  {
    current_hint_x = e.clientX;   
    current_hint_y = e.clientY;
  }
  else if(ie4)
  {
    current_hint_x = e.x + document.body.scrollLeft;
    current_hint_y = e.y + document.body.scrollTop;  
  }
  else if(nn4)
  {
    current_hint_x = e.pageX;
    current_hint_y = e.pageY;
  }
  if(enable_hint_moving)
    on_hint_mouse_move();
}

function on_hint_mouse_move() 
{
  if(current_hint_id)
  	move_hint(current_hint_id, current_hint_x+10, current_hint_y+10);
}

function show_hint(id) 
{
  if (dom) 
  {
  	document.getElementById(id).style.visibility = "visible";
   	document.getElementById(id).style.display = '';
  }
  else if (ie4) 
  {
  	document.all[id].style.visibility = "visible";
    document.all[id].style.display= '';
  } 
  else if (nn4) 
  {
  	document.hints[id].visibility = "show";
    document.hints[id].display='';
  }
}

function hide_hint(id) 
{
  if (dom) document.getElementById(id).style.visibility = "hidden";
  else if (ie4) document.all[id].style.visibility = "hidden";
  else if (nn4) document.hints[id].visibility = "hide";
}

function move_hint(id,x,y)
{
  if (dom)
  {
    h = document.getElementById(id);
    h.style.left = x;
    h.style.top = y;
  }
  else if(ie4)
  {
    document.all[id].left = x;
    document.all[id].top = y;    
  }
  else if(nn4)
  {
    document.hints[id].left = x;
    document.hints[id].top = y;
  }
}

function start_hint(id)
{
  if(current_hint_id)
  	hide_hint(current_hint_id);
  
  current_hint_id = id;
  
  move_hint(current_hint_id, current_hint_x+10, current_hint_y+10);
  show_hint(current_hint_id);
}

function stop_hint()
{
  hide_hint(current_hint_id);
  current_hint_id = null;
}

function toggle_hint(id)
{
  if(current_hint_id)
  {
    enable_hint_moving = true;
    
    if(current_hint_id == id)
    {
    	stop_hint();
    	return;
    }
    stop_hint();
  }
  enable_hint_moving = false;
  
  start_hint(id);
}