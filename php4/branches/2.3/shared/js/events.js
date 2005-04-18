function add_event(control, type, fn, use_capture)
{
 if (control.addEventListener)
 {
   control.addEventListener(type, fn, use_capture);
   return true;
 }
 else if (control.attachEvent)
 {
   var r = control.attachEvent("on" + type, fn);
   return r;
  }
}

