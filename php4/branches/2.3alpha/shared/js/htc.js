tmp_window_onload = window.onload
window.onload = function()
{
  if(window != top)
    if(top.onload_iframe)
      top.onload_iframe()
  if(tmp_window_onload) tmp_window_onload()

  apply_behavior()
}
function apply_behavior()
{

  CLASS_MAP = build_class_map(document)
  var arr = document.getElementsByTagName('*')
  for(var v in arr)
  {

    if(typeof(arr[v]) != 'undefined' && CLASS_MAP[arr[v].className])
    {
      try
      {
        var clss = window[CLASS_MAP[arr[v].className]].prototype

      }
      catch(ex)
      {
        confirm('apply_behavior() - NO CLASS DEFINED: ' + CLASS_MAP[arr[v].className])
        break
      }
      for(var j in clss)
      {
        arr[v][j] = clss[j]
      }
    }
  }
}
function build_class_map(doc)
{
  var arr = []
  for(var i = 0; i<doc.styleSheets.length; i++)
  {
    var rules = doc.styleSheets[i].rules
    if(is_gecko) rules = doc.styleSheets[i].cssRules

    for(var j = 0; j<rules.length; j++)
    {
      var rule = rules[j];
      if(rule.style.page)
      {
        arr[rule.selectorText.substr(1)] = rule.style.page
      }
    }
  }

  return arr
}

//=======================
//  ACTIONS
//=======================
function action(){}
action.prototype.onmouseover = function()
{
  this.style.backgroundImage = "url(/shared/images/rect.gif)"

}
action.prototype.onmouseout = function()
{
  this.style.backgroundImage = ""
}

function row(){}
row.prototype.onmouseover = function()
{
  this.style.backgroundColor='#F7F7F7'
}
row.prototype.onmouseout = function()
{
    this.style.backgroundColor=''
}

//
//
//
function actions(){}
actions.prototype.onmouseover = function()
{
  this.style.backgroundColor='#F7F7F7'

//  if(this.isHover)return
//  this.set_active()
//  this.isHover = true
}
actions.prototype.onmouseout = function()
{
  this.style.backgroundColor=''

//  if(this.HT()) return
//  this.set_normal()
//  this.isHover = false

}
actions.prototype.set_active = function()
{
  try{
  var acts = this.get_jip_element('actions')
  if(acts == null) return
  acts.className = 'jip-actions-active'
  var obj = this.get_jip_element('object')
  obj.className = 'jip-object-active'

  this.get_jip_element('l').src = '/shared/images/actl.gif'
  this.get_jip_element('r').src = '/shared/images/actr.gif'
  }
  catch(ex)
  {
//    alert('JIP ACTION: initialization fail')
  }
}
actions.prototype.set_normal = function()
{
  try{
  var acts = this.get_jip_element('actions')
  if(acts == null) return
  acts.className = 'jip-actions'

  this.get_jip_element('object').className = 'jip-object'

  this.get_jip_element('l').src = '/shared/images/act1.gif'
  this.get_jip_element('r').src = '/shared/images/act3.gif'
  }
  catch(ex)
  {
//    alert('JIP ACTION: initialization fail')
  }
}
//
// [ service]
//
actions.prototype.get_jip_element = function(jip_name)
{
  var arr = this.all
  for(var i=0;i<arr.length; i++)
    if(arr[i].jip == jip_name) return arr[i]
  return null
}
actions.prototype.HT = function()
{
  var obj = this.document.elementFromPoint(event.x, event.y)
  while(obj != null)
  {
    if(obj == this)return true
    obj = obj.parentElement
  }
  return false
}

