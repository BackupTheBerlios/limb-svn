/*common functions*/
function containsDOM(container, containee)
{
  var isParent = false;
  do {
    if ((isParent = container == containee))
      break;
    containee = containee.parentNode
  }
  while (containee != null);
  return isParent;
}
function checkMouseEnter(element, evt)
{
  if (element.contains && evt.fromElement) {
    return !element.contains(evt.fromElement);
  }
  else if (evt.relatedTarget) {
    return !containsDOM(element, evt.relatedTarget);
  }
}
function checkMouseLeave(element, evt)
{
  if (element.contains && evt.toElement) {
    return !element.contains(evt.toElement);
  }
  else if (evt.relatedTarget) {
    return !containsDOM(element, evt.relatedTarget);
  }
}
get_obj_by_id = function(arr,id)
{
  for(var i=0;i<arr.length;i++) if(arr[i].id == id) return arr[i]
  return null
}
get_real_offset = function(obj, dimention, coord)
{
  var y = 0
  while(obj != null)
  {
    if(obj.style.top == '') y += obj[dimention]
    else if(obj.style[coord]) y += obj.style[coord].replace('px','')*1
    obj = obj.offsetParent
  }
  return y
}

/******drop*down**************/
//===========================
// [ Class :: drop down ]
//===========================
function CDropDown(parent)
{
  this.uid = new Date().getMilliseconds()*Math.random()

  this.parent = parent
  if(!this.parent.level)this.level = 1
  else this.level = this.parent.level + 1

  this.div = behavior('CContainer', top.document.createElement('div'), this)
}
CDropDown.prototype.onmouseover = function(ev)
{
  if(!checkMouseEnter(this, (is_gecko) ? ev : event)) return
  window.clearInterval(CDropDown.interval)
  CDropDown.interval
  if(!CDropDown.queue) CDropDown.queue = []

  if(CDropDown.queue[CDropDown.level]) if(CDropDown.level >= this.level) CDropDown.hide_all(this.level)

  this.div.set_content(this.get_content())
  this.div.show()
  this.set_pos()

  CDropDown.queue[this.level] = this
  CDropDown.level = this.level
}
CDropDown.prototype.onmouseout = function(ev)
{
  if(!checkMouseLeave(this, (is_gecko) ? ev : event)) return
  CDropDown.interval = window.setInterval('CDropDown.hide_all(1)',1000)
}

CDropDown.hide_all = function(lev)
{
  for(var i=lev*1; i<=CDropDown.level; i++)
  CDropDown.queue[i].div.hide()
}
CDropDown.prototype.set_pos = function()
{
  if(!this.shift)
  {
    var cssText = CSSMAP[this.className]
    cssText = cssText ? cssText.cssText : ''
    this.shift = this.get_shift(cssText)
  }
//	var shift = {t:0,r:0,b:0,l:0}
  var wdiv = this.div.style.width.replace('px','') * 1
  var hdiv = this.div.style.height.replace('px','') * 1
  var w = this.offsetWidth
  var h = this.offsetHeight
  var l = get_real_offset(this, 'offsetLeft', 'left')
  var t = get_real_offset(this, 'offsetTop', 'top')
  var r = l + w
  var b = t + h
  var win_width = top.document.body.clientWidth + top.document.body.scrollLeft - 10
  var win_height = top.document.body.clientHeight + top.document.body.scrollTop - 10
  var ddalign = this.init_obj.getAttribute('ddalign')
  if(!ddalign) ddalign = 'hbr'
  var arr = ddalign.split('')
  var mask = []
  for(var i=0;i<arr.length;i++) mask[arr[i]] = 1

  var succ = 0
  var i = 0
  do{
  i++
  if(mask['h'])
  {
    if(mask['t'])
    {
      this.div.style.top = t + h - hdiv - this.shift['t']
      if(t + h - hdiv - this.shift['t'] < 0) succ = this.swap_dir(mask,'t')
    }
    if(mask['b'])
    {
      this.div.style.top = t + this.shift['b']
      if(t + hdiv + this.shift['b'] > win_height) succ = this.swap_dir(mask,'b')
    }
    if(mask['r'])
    {
      this.div.style.left = r + this.shift['r']
      if(r + wdiv + this.shift['r'] > win_width) succ = this.swap_dir(mask,'r')
    }
    if(mask['l'])
    {
      this.div.style.left = l - wdiv - this.shift['l']
      if(l - wdiv - this.shift['l'] < 0) succ = this.swap_dir(mask,'l')
    }
  }
  if(mask['v'])
  {
    if(mask['t'])
    {
      this.div.style.top = t - hdiv - this.shift['t']
      if(t - hdiv - this.shift['t'] < 0) succ = this.swap_dir(mask,'t')
    }
    if(mask['b'])
    {
      this.div.style.top = t + h + this.shift['b']
      if(t + h + hdiv + this.shift['b']> win_height) succ = this.swap_dir(mask,'b')
    }
    if(mask['r'])
    {
      this.div.style.left = l - this.shift['l']
      if(l + wdiv - this.shift['l'] > win_width) succ = this.swap_dir(mask,'r')
    }
    if(mask['l'])
    {
      this.div.style.left = l + w - wdiv + this.shift['r']
      if(l + w - wdiv + this.shift['r'] < 0) succ = this.swap_dir(mask,'l')
    }
  }
  }while(succ && i<5)
}

CDropDown.prototype.swap_dir = function(arr, l)
{
  if(l == 'b') { arr['b'] = 0; arr['t'] = 1; }
  if(l == 't') { arr['t'] = 0; arr['b'] = 1; }

  if(l == 'r') { arr['r'] = 0; arr['l'] = 1; }
  if(l == 'l') { arr['l'] = 0; arr['r'] = 1; }
  return true
}

CDropDown.prototype.get_shift = function(str)
{
  return {'l':this.get_coord(str,'left'),'r':this.get_coord(str,'right'),'t':this.get_coord(str,'top'),'b':this.get_coord(str,'bottom')}
}

CDropDown.prototype.get_coord = function(str, label)
{
  var expression = "(("+label.toLowerCase()+"|"+label.toUpperCase()+"):([ 0-9 -]*)px)"
  var re = new RegExp(expression,"gim")
  re.exec(str)
  if(RegExp.$2.toLowerCase() == label) return RegExp.$3*1
  return 0
}
CDropDown.prototype.get_content = function()
{
  return "<table width=100% border=0 cellspacing=1 cellpadding=10 class=com1><tr><td class=com4>\
  <span behavior=CDropDown ddalign=hbr><img src='images/icon/s/i1.gif'>[fill me]</span>\
  </td></tr></table>"
}


//===========================
// [ Class :: drop down :: container]
//===========================
CContainer = function(parent)
{
  this.style.position = 'absolute'
  this.style.width = this.style.height = 0
  if(!top.zIndex)top.zIndex = 1000; else top.zIndex += 10
  this.style.zIndex = top.zIndex++
  top.document.body.appendChild(this)
  this.parent = parent

  this.create_iframe()
}
CContainer.prototype.create_iframe = function()
{
  if(is_gecko)
  {
    this.ifr = this
    return
  }
  if(!CContainer.arr_ifrs)CContainer.arr_ifrs = []
  if(!CContainer.arr_ifrs[this.parent.level])
  {
    var ifr = top.document.createElement('iframe')
    var ifs = ifr.style
    ifs.cssText = 'position:absolute; width:0;height:0;'
    ifr.scrolling = 'no'
    ifr.frameBorder = 0
    top.document.body.appendChild(ifr)
    CContainer.arr_ifrs[this.parent.level] = ifr
  }
  this.ifr = CContainer.arr_ifrs[this.parent.level]
  this.ifr.style.zIndex = top.zIndex
  this.style.zIndex = top.zIndex + 10
}
CContainer.prototype.show = function()
{
  var obs = this.style
  var ifs = this.ifr.style
  obs.display = 'block'
  ifs.display = 'block'
  ifs.left = obs.left
  ifs.top = obs.top
  ifs.width = this.offsetWidth
  ifs.height = this.offsetHeight

  if(!this.is_filling)
  {
    this.is_filling = 1
    this.innerHTML = this.content
  }

  this.style.width = this.firstChild.offsetWidth
  this.style.height = this.firstChild.offsetHeight
  ifs.zIndex = 1000
  obs.zIndex = 10000
}
CContainer.prototype.hide = function()
{
  this.style.display = 'none'
  this.ifr.style.display = 'none'
}
CContainer.prototype.set_content = function(content)
{
  this.content = '<div>' + content + '</div>'
}
CContainer.prototype.onmouseover = function(ev)
{
  if(!this.is_definded)
  {
    this.is_definded = 1
    apply_behavior(this, this.parent)
  }
  if(!checkMouseEnter(this, (is_gecko) ? ev : event)) return
  window.clearInterval(CDropDown.interval)
}
CContainer.prototype.onmouseout = function(ev)
{
  if(!checkMouseLeave(this, (is_gecko) ? ev : event)) return
  CDropDown.interval = window.setInterval('CDropDown.hide_all(1)',1000)
}


/******show*hide**************/
//===========================
// [ Class :: show hide ]
//===========================
function CShowHide()
{
  var obj_minus, obj_body
  if(this.id == '')
  {
    obj_minus = get_obj_by_id(this.getElementsByTagName('span'),'minus')
    obj_body = get_obj_by_id(this.getElementsByTagName('span'),'body')
  }
  else
  {
    obj_minus = document.getElementById(this.id + '_minus')
    obj_body = document.getElementById(this.id + '_body')
  }
  obj_minus.this_obj = this
  obj_minus.onclick = function()
  {
    if(!obj_body.is_mirr)
    {
      this.this_obj.replace_src(obj_minus)
      obj_body.style.display = 'none'
      obj_body.is_mirr = true
    }
    else
    {
      this.this_obj.replace_src(obj_minus)
      obj_body.style.display = 'block'
      obj_body.is_mirr = false
    }
  }
}
CShowHide.prototype.replace_src = function(obj)
{
  var arr = obj.getElementsByTagName('*')
  var dir = 0
  for(var i=0;i<arr.length;i++) if(arr[i].src) if(arr[i].src.indexOf('minus')!=-1){dir = 1;break}
  var str1 = 'minus'
  var str2 = 'plus'
  if(!dir)
  {
    str1 = 'plus'
    str2 = 'minus'
  }
  for(var i=0;i<arr.length;i++) if(arr[i].src) arr[i].src = arr[i].src.replace(str1,str2)
}

