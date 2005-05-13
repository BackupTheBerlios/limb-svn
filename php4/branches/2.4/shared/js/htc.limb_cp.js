get_obj_by_id = function(arr,id)
{
  for(var i=0;i<arr.length;i++) if(arr[i].id == id) return arr[i]
  return null
}

get_real_offset = function(obj, dim, one_win)
{
	
	var res = 0
	var win = is_gecko ? obj.ownerDocument.defaultView : obj.document.parentWindow
	if(!win)win = window
	var is_top = win == top ? 2 : 0
	do
	{
		while(obj != null)
		{
			
			var suffix = dim.charAt(0).toUpperCase() + dim.substr(1)
			if(obj.style[dim] == '') res += obj['offset'+suffix]
			else if(obj.style[dim]) res += parseInt(obj.style[dim])
			
			res -= obj['scroll'+suffix]
//			if(obj.className == 'CDDMenu-i-hint')
//			alert([obj, obj.className, res, obj['offset'+suffix]])

			obj = obj.offsetParent
		}
		obj = win.frameElement
		win = win.parent
		if(win == top)
			is_top++
			
//			alert('--' + res)
	}
	while(is_top < 2 && !one_win)
	return res
	
}

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
	this.mouseover(ev)
}
CDropDown.prototype.mouseover = function(ev, disable_checkMouseEnter)
{
	top.clearInterval(top.CDropDown.interval)
	if(!disable_checkMouseEnter)
	if(!checkMouseEnter(this, is_gecko ? ev : event)) return

	if(!top.CDropDown.queue) top.CDropDown.queue = []
	
	if(top.CDropDown.queue[top.CDropDown.level]) if(top.CDropDown.level >= this.level) CDropDown.hide_all(this.level)

	if(!this.is_filling)
	this.div.set_content(this.get_content())
	this.div.show()
	this.set_pos()
	this.div.show()
	if(this.onShow) this.onShow()
	
	top.CDropDown.queue[ this.level ] = this
	top.CDropDown.level = this.level
}
CDropDown.prototype.onmouseout = function(ev)
{
	this.mouseout(ev)
}
CDropDown.prototype.mouseout = function(ev, disable_checkMouseLeave)
{
	if(!disable_checkMouseLeave)
	if(!checkMouseLeave(this, is_gecko ? ev : event)) return
	
	top.clearInterval(top.CDropDown.interval)
	top.CDropDown.interval = top.setInterval('CDropDown.hide_all(1)',1000)	
}

CDropDown.hide_all = function(lev)
{
	for(var i=lev*1; i<=top.CDropDown.level; i++)
	{
		top.CDropDown.queue[i].div.hide()
		if(top.CDropDown.queue[i].onClose) top.CDropDown.queue[i].onClose()
	}
	
}
CDropDown.hitTest = function(ev)
{
	var tar = ev.target
	if(!is_gecko) tar = ev.srcElement
	return containsDOM(top.CDropDown.queue[top.CDropDown.level].div, tar)
}
CDropDown.prototype.trace = function(obj)
{
	var str = ''
	var i=0
	for(var v in obj)
	{
		if(typeof(obj[v]) == 'function') continue
		if(!parseInt(obj[v])) continue
		str += v + '=' + obj[v] + ';'
		if(!(i%2)) str += '\n'
		i++
	}
	alert(str)
}
CDropDown.prototype.set_pos = function()
{
	if(!this.shift)
	{
		var cssText = CSSMAP[this.init_obj.className]
		cssText = cssText ? cssText.cssText : ''
		this.shift = this.get_shift(cssText)
	}
	var sl = this.shift.l
	var sr = this.shift.r
	var st = this.shift.t
	var sb = this.shift.b
//	alert(sr)
	if(is_gecko)
	{
		var mo = this.get_shift(this.getAttribute('moz-offset'))
//		alert([mo.l, mo.r, mo.t, mo.b])
		sl += mo.l
		sr += mo.r
		st += mo.t
		sb += mo.b
	}
//	alert(sr)
//	this.shift = {t:0,r:0,b:0,l:0}
//	var wdiv = parseInt(this.div.style.width)
//	var hdiv = parseInt(this.div.style.height)
	var wdiv = this.div.firstChild.offsetWidth
	var hdiv = this.div.firstChild.offsetHeight
	var w = this.offsetWidth
	var h = this.offsetHeight
	var l = get_real_offset(this, 'left')
	var t = get_real_offset(this, 'top')
	var r = l + w
	var b = t + h
	var win_width = top.document.body.clientWidth + top.document.body.scrollLeft - 1
	var win_height = top.document.body.clientHeight + top.document.body.scrollTop - 1

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
			this.div.style.top = t + h - hdiv - st 
			if(t + h - hdiv - st < 0) succ = this.swap_dir(mask,'t')
		}
		if(mask['b'])
		{
			this.div.style.top = t + sb
			if(t + hdiv + sb > win_height) succ = this.swap_dir(mask,'b')
		}
		if(mask['r'])
		{
			this.div.style.left = r + sr
			if(r + wdiv + sr > win_width) succ = this.swap_dir(mask,'r')
		}
		if(mask['l'])
		{
			this.div.style.left = l - wdiv - sl
			if(l - wdiv - sl < 0) succ = this.swap_dir(mask,'l')
		}
	}
	if(mask['v'])
	{
		if(mask['t'])
		{
			this.div.style.top = t - hdiv - st
			if(t - hdiv - st < 0) succ = this.swap_dir(mask,'t')
		}
		if(mask['b'])
		{
			this.div.style.top = t + h + sb
			if(t + h + hdiv + sb> win_height) succ = this.swap_dir(mask,'b')
		}
		if(mask['r'])
		{
			this.div.style.left = l - sl
			if(l + wdiv - sl > win_width) succ = this.swap_dir(mask,'r')
		}
		if(mask['l'])
		{
			this.div.style.left = l + w - wdiv + sr
			if(l + w - wdiv + sr < 0) succ = this.swap_dir(mask,'l')
		}
	}
	}while(succ && i<5)

}

CDropDown.prototype.swap_dir = function(arr, l)
{
	if(arr['d']) return
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
	if(RegExp.$2.toLowerCase() == label) return parseInt(RegExp.$3)
	return 0
}
CDropDown.prototype.get_content = function()
{
	return "<table width=100% border=0 cellspacing=10 cellpadding=0 class=com1>\
	<tr class=com4><td><img src='images/icon/s/i1.gif'align=left></td><td><span behavior=CDropDown ddalign=hbr>redefine method CDropDown.prototype.get_content</span></td></tr>\
	</table>"
}
CDropDown.prototype.draw_shadow = function()
{
	if(is_gecko)
	{
		var shad = document.createElement('img')
		shad.src = '../shared/images/main/tab1/shad.png'
		if(!this.shad)
		{
			var width = this.div.offsetWidth
			this.div.appendChild(shad)
			shad.style.height = shad.offsetHeight
			shad.style.width = width
		}
		this.shad = shad
	}
}


//===========================
// [ Class :: drop down :: container]
//===========================
CContainer = function(parent)
{
	
	this.style.position = 'absolute'
	this.style.width = this.style.height = 0
	if(!top.zIndex)top.zIndex = 10000; else top.zIndex += 10
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
	
	obs.zIndex = top.zIndex++
	ifs.zIndex = obs.zIndex-1
}
CContainer.prototype.hide = function()
{
	this.style.display = 'none'
	this.ifr.style.display = 'none'
	top.clearAllIntervals(top.CDropDown.interval)
	top.clearAllIntervals(top.CDropDown.interval)
}
CContainer.prototype.set_content = function(content)
{
	this.content = '<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>' + content + '</td></tr></table>'
}
CContainer.prototype.onmouseover = function(ev)
{
	if(!this.is_definded)
	{
		this.is_definded = 1
		apply_behavior(this, this.parent, 'span', 'behavior')
	}
	top.clearInterval(top.CDropDown.interval)
	if(!checkMouseEnter(this, is_gecko ? ev : event)) return
	
}
CContainer.prototype.onmouseout = function(ev)
{
	if(!checkMouseLeave(this, is_gecko ? ev : event)) return
	top.clearInterval(top.CDropDown.interval)
	top.CDropDown.interval = top.setInterval('CDropDown.hide_all(1)',1000)	
}

ddmousedown = function(ev)
{
	top.clearInterval(top.CDropDown.interval)
//	if(window != window.parent) window.parent.ddmousedown()
	try{
	if(top.CDropDown.queue[top.CDropDown.level].disable_mousedown) return
	if(	top.CDropDown.queue[top.CDropDown.level].disable_close_click_inside	)
	{
		if(	!top.CDropDown.hitTest(is_gecko ? ev : event)	)
			top.CDropDown.interval = top.setInterval('CDropDown.hide_all(1)', 100)	
	}
	else
			top.CDropDown.interval = top.setInterval('CDropDown.hide_all(1)', 100)	
	}
	catch(ex){}
}

add_event(window.document, 'mousedown', ddmousedown, true)

top.clearAllIntervals = function(interval_id)
{
	if(!interval_id)return
	for(var i=interval_id-100; i<interval_id+100; i++)
	{
		top.clearInterval(i)
	}
}


/******show*hide**************/
//===========================
// [ Class :: show hide ]
//===========================
function CShowHide()
{
	var obj_minus = document.getElementById(this.id + '_minus')
	var obj_body = document.getElementById(this.id + '_body')
	var this_obj = this
	obj_minus.show = function()
	{
		obj_body.style.display = 'block'
	}
	obj_minus.hide = function()
	{
		obj_body.style.display = 'none'
	}
	obj_minus.onclick = function()
	{
		this.change()
		setCookieWithId('CShowHide', this_obj.id, obj_body.style.display)
	}
	obj_minus.change = function()
	{
		if(getCookieWithId('CShowHide', this_obj.id) == 'block')
			this.hide()
		else
			this.show()
		this_obj.replace_src(obj_minus)
	}
	obj_minus.style.cursor = 'hand'
	var c = getCookieWithId('CShowHide', this.id)
	var display = isset(c) ? c  : 'block'
	obj_body.style.display = display
	this.replace_src(obj_minus, display != 'block')
	setCookieWithId('CShowHide', this_obj.id, display)
	
}
CShowHide.prototype.replace_src = function(obj, dir)
{
	var arr = obj.getElementsByTagName('*')
	if(!isset(dir))
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


CCookie = function()
{
	switch(this.type)
	{
		case "checkbox":
			_extends(this, CCookieCheckbox)
		break
		case "text":
			_extends(this, CCookieInput)
		break
		case "radio":
			_extends(this, CCookieRadio)
		break
	}
	switch(this.tagName)
	{
		case "SELECT":
			_extends(this, CCookieSelect)
		break
	}
	add_event(this, 'change', this.change, true)
	add_event(this, 'click', this.click, true)
}
CCookie.prototype.change = function()
{
}
CCookie.prototype.click = function()
{
}//
//
//
CCookieCheckbox = function()
{
	var c = getCookieWithId('CCookie', this.id)
	if(c)
		this.checked = parseBool(c)
}
CCookieCheckbox.prototype.click = function()
{
	setCookieWithId('CCookie', this.id, this.checked)
}
//
//
//
CCookieInput = function()
{
	var c = getCookieWithId('CCookie', this.id)
	if(c) this.value = c
}
CCookieInput.prototype.change = function()
{
	if(isNaN(this.value))return
	setCookieWithId('CCookie', this.id, this.value)
}
//
//
//
CCookieSelect = function()
{
	var c = getCookieWithId('CCookie', this.id)
	if(c) this.selectedIndex = c
}
CCookieSelect.prototype.change = function()
{
	if(isNaN(this.value))return
	setCookieWithId('CCookie', this.id, this.selectedIndex)
}
//
//
//
CCookieRadio = function()
{
	var coo = getCookieWithId('CCookie', this.id)
//	alert(coo)
	if(coo)
	{
		this.checked = false
		if(coo == this.id) this.checked = true
	}
}
CCookieRadio.prototype.click = function()
{
	setCookieWithId('CCookie', this.name, this.id)
}
