function get_mo_name(filename, s1, s2)
{
  var arr = filename.split('/')
  var fn = arr[arr.length-1]
  arr[arr.length-1] = ''
  return arr.join('/') + fn.replace(s1, s2)
}

//===========================
// [ Class :: drop down :: CDDAction]
//===========================
CDDAction = function(parent)
{
  this._super(parent)
}
_extends(CDDAction, CDropDown)
CDDAction.prototype.get_content = function()
{
  var arr = arr_actions[this.init_obj.id]
  var str = '<table border=0 cellspacing=0 cellpadding=0 class=dd-menu-container style="border-left:solid 1px #0000B5">'
  for(var v in arr)
  {
    if(typeof(arr[v])!='string')continue
    str += '<tr>'
    str += '<td><span behavior="CRow" target="-2"></span><img src="' + arr_action_types[v]['icon'] + '"></td>'
    str += '<td><nobr><a href="' + arr[v] + '">' + arr_action_types[v]['title'] + '</a></nobr></td>'
    str += '</tr>'
  }
  str += '</table>'
  return str
  return str
}
CDDAction.prototype.onShow = function()
{
  this.draw_shadow()
}

//===========================
// [ Class :: drop down :: CDDTab]
//===========================
CDDTab = function(parent)
{
  this._super(parent)
  this.disable_mousedown = 1
}
_extends(CDDTab, CDropDown)
CDDTab.prototype.get_content = function()
{
  var img = this.getElementsByTagName('img')[0]
  var is_active = this.parentNode.parentNode.parentNode.parentNode.parentNode.className == 'tab-active'
  var fn = 'url(/shared/images/main/tab1/arr.gif)'
  if(is_active) fn = get_mo_name(fn, '.', '_.')
  img.style.backgroundImage = get_mo_name(fn, '.', '-.')

  var content_obj = get_obj_by_id(this.getElementsByTagName('span'),'content')
  return content_obj.innerHTML
}
CDDTab.prototype.onShow = function()
{
  this.draw_shadow()
}
CDDTab.prototype.onClose = function()
{
  var img = this.getElementsByTagName('img')[0]
  img.style.backgroundImage = ''
}
CDDTab.prototype.onmouseover = function(ev)
{
}
CDDTab.prototype.onmousedown = function(ev)
{
  ev = is_gecko ? ev : event
  if(this.div.style.display != 'block')
  {
    this.mouseover(ev, true)
    this.disable_mousedown = 0
    ev.cancelBubble = true
  }
  else
  {
    CDropDown.hide_all(1)
    this.disable_mousedown = 1
  }
}
//===========================
// [ Class :: row hightlight ]
//===========================
function CRow()
{
}
CRow.prototype.onmouseover = function()
{
  this.style.backgroundColor='#D8FFB7'
}
CRow.prototype.onmouseout = function()
{
  this.style.backgroundColor=''
}
CRow.prototype.onmousedown = function()
{
//  var a = this.getElementsByTagName('a')
//  if(a.length)
//  {
//    window.location.href = a[0].href
//  }
//  var a = this.getElementsByTagName('input')
//  for(var v in a)
//  {
//    if(a[v].type == 'checkbox')
//    {
//      a[v].checked = !a[v].checked
//    }
//  }
//	this.onmouseout()
}

//===========================
// [ Class :: drop down :: menu]
//===========================
CDDMenu = function(parent)
{
  this._super(parent)
}
_extends(CDDMenu, CDropDown)
CDDMenu.prototype.get_content = function()
{
  var img = this.getElementsByTagName('img')[0]
  if(!this.init_obj.noimage)
  img.src = get_mo_name(img.src, '.', '-.')

  var content_obj = get_obj_by_id(this.getElementsByTagName('span'), 'content')
  return content_obj.innerHTML
}
CDDMenu.prototype.onClose = function()
{
  var img = this.getElementsByTagName('img')[0]
  if(!this.init_obj.noimage)
  img.src = get_mo_name(img.src, '-.', '.')
}
CDDMenu.prototype.onShow = function()
{
  this.draw_shadow()
}
CDDMenu.prototype.onmouseover = function(ev)
{
  this.mouseover(ev, true)
}
CDDMenu.prototype.onmouseout = function(ev)
{
  this.mouseout(ev, true)
}
//===========================
// [ Class :: drop down :: layout]
//===========================
CDDLayout = function(parent)
{
  this._super(parent)
  this.disable_close_click_inside = 1
  this.restore_cookie()
  top.LAYOUT_CONTROL = this
}

_extends(CDDLayout, CDropDown)
CDDLayout.prototype.get_content = function()
{
  var content_obj = get_obj_by_id(this.getElementsByTagName('span'),'content')
  return content_obj.innerHTML
}
CDDLayout.prototype.onShow = function()
{
  var arr = this.div.getElementsByTagName('*')
  this_obj = this
  this_obj.arr_items = []
  for(var v in arr)
  {
    var obj = arr[v]
    if(obj.className)
    if(obj.className.indexOf('lo-')!=-1)
    {
      obj.set_color = function(clr)
      {
        this.tmp_clr = this.style.backgroundColor
        this.style.backgroundColor = clr
      }
      obj.reset_color = function(clr)
      {
        if(clr) this.tmp_clr = clr
        this.style.backgroundColor = this.tmp_clr
      }
      obj.onmouseover = function()
      {
        if(!this.elem) return
        this.set_color('#D8FFB7')
      }
      obj.onmouseout = function()
      {
        if(!this.elem) return
        if(get_cookie(this.className) != 'none')
          this.reset_color('#9595FF')
        else
          this.reset_color('#C0C0C0')
      }
      obj.onmousedown = function()
      {
        this.showhide()
      }
      obj.showhide =
      function(show)
      {
        if(!this.locate_elem())return

        if(get_cookie(this.className) == 'none' || show)
          set_cookie(this.className, 'block')
        else
          set_cookie(this.className, 'none')

        this.display = get_cookie(this.className)

        var name = this.className.replace('lo-', '')
        switch(name)
        {
          case "top":
            this.elem.firstChild.style.display = this.display
          break
          case "center":
            try{
            this_obj.arr_items['lo-left'].showhide(0)
            this_obj.arr_items['lo-right'].showhide(0)
            this_obj.arr_items['lo-top'].showhide(0)
            }
            catch(ex){}
          break
          case "left":
          case "right":
            this.elem.parentNode.style.display = this.display
          break
        }
        try{
        this.elem.ownerDocument.resize_content()
        this.elem.ownerDocument.resize_content()
        }catch(ex){}
        this.check_colors()
      }
      obj.locate_elem = function()
      {
        return this_obj.locate_elem(this)
      }
      obj.check_colors = function()
      {
        this.locate_elem()
        if(get_cookie(this.className) == 'none' || !this.elem)
          this.set_color('#C0C0C0')
        else
          this.reset_color('#9595FF')
      }
      obj.check_colors()
      this_obj.arr_items[obj.className] = obj
    }
  }
}

CDDLayout.prototype.locate_elem = function(obj)
{
  var name = obj.className.replace('lo-', 'lo-place-')
  obj.elem = document.getElementById(name)
  if(!obj.elem)
  {
    var frm = get_frame(tabs.active_tab.id)
    obj.elem = frm.document.getElementById(name)
  }
  if(!obj.elem)return null
  return obj.elem
}
CDDLayout.prototype.restore_cookie = function()
{
  var content_obj = get_obj_by_id(this.getElementsByTagName('span'),'content')
  var arr = content_obj.getElementsByTagName('*')
  for(var v in arr)
  {
    var obj = arr[v]
    if(obj.className)
    if(obj.className.indexOf('lo-')!=-1)
    {
      var elem = this.locate_elem(obj)
      if(!elem) continue
      obj.display = get_cookie(obj.className)
      var name = obj.className.replace('lo-', '')
      try{
      switch(name)
      {
        case "top":
          elem.firstChild.style.display = obj.display
        break
        case "left":
        case "right":
          elem.parentNode.style.display = obj.display
        break
      }
      elem.ownerDocument.resize_content()
      elem.ownerDocument.resize_content()
      }catch(ex){}
    }
  }
}
CDDLayout.prototype.onLoadPage = function()
{
  this.restore_cookie()
}

//===========================
// [ Class :: row hightlight ]
//===========================
function CPager()
{
  var arr = this.getElementsByTagName('td')
  for(var i=0; i < arr.length; i++)
  {
    var href = arr[i].getElementsByTagName('a')
    if(!href.length)continue
    arr[i].url = href[0]
    arr[i].onmouseover = function()
    {
      this.style.backgroundColor='#D8FFB7'
    }
    arr[i].onmouseout = function()
    {
      this.style.backgroundColor=''
    }
    arr[i].onmousedown = function()
    {
      window.location.href = this.url
    }
  }
}

