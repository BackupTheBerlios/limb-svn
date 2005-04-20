include('/shared/js/md5.js');

function change_form_action(form, action)
{
  if(!form)
    return;

  form.action = action;
}

function add_form_action_parameter(form, parameter, val)
{
  if(!form)
    return;

  form.action = set_http_get_parameter(form.action + '', parameter, val);
}

function add_form_hidden_parameter(form, parameter, val)
{
  if(!form)
    return;

  hidden = document.getElementById(parameter + '_hidden_parameter');
  if(hidden)
  {
    hidden.value = val;
    form.appendChild(hidden);
  }
  else
  {
    hidden = document.createElement('INPUT');
    hidden.id = parameter + '_hidden_parameter';
    hidden.type = 'hidden';
    hidden.name = parameter;
    hidden.value = val;
    form.appendChild(hidden);
  }
}

function submit_form(form, form_action)
{
  is_popup = form_action.indexOf('popup=1');
  if(is_popup > -1)
  {
    window_name = 'w' + hex_md5(form_action) + 's';
    w = popup(LOADING_STATUS_PAGE, window_name);
    form.target = w.name;
  }

  if(form_action)
    form.action = form_action;

  form.submit();
}

function submit_grid_form(button, selector_id)
{
  menu = document.getElementById(selector_id);
  action = menu.options[menu.selectedIndex].value;
  if(action != '')
    submit_form(button.form, action);
}

function process_action_control(droplist)
{
  if (typeof(droplist.value) != 'undefined')
    value = droplist.value;
  else
    value = droplist[0].value;

  submit_form(droplist.form, value);
}

function sync_action_controls(obj)
{
  col = obj.form.elements[obj.name];
  if (typeof(col.length) != 'undefined' && col.length>0)
    for(i=0; i<col.length; i++)
    {
      col(i).selectedIndex = obj.selectedIndex;
    }
}

function transfer_value(target_id, transfer_value)
{
  obj = document.getElementById(target_id);
  if(obj)
  {
    obj.value = transfer_value;
  }
}

function transfer_img_src(target_id, transfer_src)
{
  obj = document.getElementById(target_id);
  if(obj)
  {
    obj.src = transfer_src;
  }
}

function bulk_options(start, end, selected, options_attrs)
{
  options = '';
  for(i = start; i <= end; i++)
    if (i == selected) options += '<option value=' + i + ' selected ' + options_attrs + '>'+i;
      else options += '<option value=' + i + ' ' + options_attrs + '>'+i;
  document.write(options)
}
