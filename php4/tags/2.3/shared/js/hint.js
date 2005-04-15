var current_hint_x = -1, current_hint_y = -1;
var current_hint_id = null;
var enable_hint_moving = true;

add_event(document, 'mousemove', hint_mouse_move_handler);

function hint_mouse_move_handler(e)
{
  if (is_gecko)
  {
    current_hint_x = e.pageX;
    current_hint_y = e.pageY;
  }
  else
  {
    e = window.event;
    current_hint_x = e.x + document.body.scrollLeft;
    current_hint_y = e.y + document.body.scrollTop;
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
 	document.getElementById(id).style.visibility = "visible";
 	document.getElementById(id).style.display = '';
}

function hide_hint(id)
{
  document.getElementById(id).style.visibility = "hidden";
}

function move_hint(id,x,y)
{
  h = document.getElementById(id);
  h.style.left = x;
  h.style.top = y;
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
  if(current_hint_id)
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