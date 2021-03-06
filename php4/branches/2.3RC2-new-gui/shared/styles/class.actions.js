//===========================
// [ Class :: drop down :: CDDAction]
//===========================
var arr_actions = new Array();
CDDAction = function(parent)
{
  this._super(parent)
}
_extends(CDDAction, CDropDown)
CDDAction.prototype.get_content = function()
{
  var arr = arr_actions[this.init_obj.id]
  var str = '<table border="0" cellspacing="0" cellpadding="0" class="dd-action-container">'
  for(var v in arr)
  {
    if(v == '_')continue;
    str += '<tr>'
    str += '<td><img src="' + arr[v]['img'] + '"></td>'
    str += '<td><nobr><a href="' + arr[v]['href'] + '" onclick="return click_href(this.href);">' + arr[v]['name'] + '</a></nobr></td>'
    str += '</tr>'
  }
  str += '</table>'
  return str
}

//===========================
// [ Class :: drop down :: CDDGridAction]
//===========================
CDDGridAction = function(parent)
{
  this._super(parent)
}
_extends(CDDGridAction, CDropDown)
CDDGridAction.prototype.get_content = function()
{
  var arr = arr_actions[this.init_obj.id]
  var grid_form = "document.getElementById('grid_form_" + this.init_obj.id + "')";

  var str = '<table border="0" cellspacing="0" cellpadding="0" class="dd-container">'
  for(var v in arr)
  {
    if(v == '_') continue;
    str += '<tr>'
    str += '<td nowrap><a href="' + arr[v]['href'] + '" onclick="submit_form(' + grid_form + ', this.href); return false;">' + arr[v]['name'] + '</a></td>'
    str += '</tr>'
  }
  str += '</table>'
  return str
}

//===========================
// [ Class :: drop down :: CDDCommon]
//===========================
var arr_details = new Array();
CDDCommon = function(parent)
{
  this._super(parent)
}
_extends(CDDCommon, CDropDown)
CDDCommon.prototype.get_content = function()
{
  var content_obj = get_obj_by_id(this.getElementsByTagName('span'),'content')
  return content_obj.innerHTML
}

//===========================
// [ Class :: row hightlight ]
//===========================
function CRow(){}
CRow.prototype.onmouseover = function()
{
  this.style.backgroundColor='#D8FFB7'
}
CRow.prototype.onmouseout = function()
{
    this.style.backgroundColor=''
}


