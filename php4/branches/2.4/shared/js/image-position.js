CImagePosition = function(parent_id, image_id, title_id, preview_id)
{
	this.constrain_props = document.getElementById('constrain_props')
	this.parent_id = parent_id
	this.image_id = image_id
	this.title_id = title_id
	this.preview_id = preview_id
	this.scale = 0.25
}
CImagePosition.prototype.init = function()
{
	if(this.is_init) return
	this.is_init = 1

	this.par = document.getElementById(this.parent_id)
	this.img = document.getElementById(this.image_id)
	this.ialign = this.par.getElementsByTagName('select')[0]
	this.title = document.getElementById(this.title_id)
	this.preview = document.getElementById(this.preview_id)
	
	this.on_change_title(this.title)

	var arr = this.par.getElementsByTagName('input')
	this_obj = this
	for(var i=0; i<arr.length; i++)
	{
		arr[i].onchange = function()
		{
			this_obj.updateStyle()
		}
		this[arr[i].id] = arr[i]
	}
	this.m_width.onchange = function()
	{
		this_obj.onWidth(this.value)
	}
	this.m_height.onchange = function()
	{
		this_obj.onHeight(this.value)
	}
	

	this.w0 = parseInt(this.img.width)
	this.h0 = parseInt(this.img.height)
	
	this.ratio = this.h0/this.w0
	
	this.m_width.value = this.w0	
	this.m_height.value = this.h0	

	this.m_border.value = this.img.border	

	this.m_top.value 		= parseInt(this.img.style.marginTop)
	this.m_right.value	= parseInt(this.img.style.marginRight)
	this.m_bottom.value	= parseInt(this.img.style.marginBottom)
	this.m_left.value		= parseInt(this.img.style.marginLeft)
	
	try{
	this.ialign.selectedIndex = this.get_index(this.img.align)
	}catch(ex){}	
	
	var cp = parseBool(get_cookie('constrain_props' + this.img.id))
	this.set_constrain_props(cp)

	this.updateStyle()
}
CImagePosition.prototype.get_index = function(val)
{
	var arr = this.ialign.options
	for(var i=0; i<arr.length; i++)
	{
		if(arr[i].value.toLowerCase() == val.toLowerCase())
			return i
	}
	return  0 
}
CImagePosition.prototype.updateStyle = function()
{
	this.img.align = this.ialign.options[this.ialign.selectedIndex].value
	this.set_val(this.img.style, 'marginTop', this.m_top.value)
	this.set_val(this.img.style, 'marginRight', this.m_right.value)
	this.set_val(this.img.style, 'marginBottom', this.m_bottom.value)
	this.set_val(this.img.style, 'marginLeft', this.m_left.value)
	this.set_val(this.img, 'width', this.m_width.value)
	this.set_val(this.img, 'height', this.m_height.value)
	try{
	var brd = this.set_val({}, 'brd', this.m_border.value) / this.scale
	this.img.style.border = 'solid ' + brd + 'px'
	}catch(ex){}
}

CImagePosition.prototype.set_val = function(obj, prop, val)
{
	val = parseInt(val)
	if(isNaN(val) || val == '' || !isset(val)) return
	obj[prop] = val * this.scale
	return obj[prop]
}

CImagePosition.prototype.onWidth = function(val)
{
	var cp = parseBool(get_cookie('constrain_props' + this.img.id))
	if(cp)
	{
		this.m_height.value = this.ratio * val
	}
	this.updateStyle()
}
CImagePosition.prototype.onHeight = function(val)
{
	var cp = parseBool(get_cookie('constrain_props' + this.img.id))
	if(cp)
	{
		this.m_width.value =  val / this.ratio
	}
	this.updateStyle()
}
CImagePosition.prototype.on_constrain_props = function()
{
	var cp = parseBool(get_cookie('constrain_props' + this.img.id))
	set_cookie('constrain_props' + this.img.id, !cp)
	this.set_constrain_props(!cp)
	this.onWidth(this.m_width.value)
}
CImagePosition.prototype.set_constrain_props = function(is_cp)
{
	if(is_cp)
		this.constrain_props.src = get_mo_name(this.constrain_props.src, '-.', '.')
	else
		this.constrain_props.src = get_mo_name(this.constrain_props.src, '.', '-.')
}
CImagePosition.prototype.on_reset_dimentions = function()
{
	this.m_width.value = this.w0
	this.m_height.value = this.h0
	this.updateStyle()
}
CImagePosition.prototype.draw_current_scale = function()
{
	document.write('1:' + 1/this.scale)
}
CImagePosition.prototype.on_change_title = function(obj)
{
	this.img.title = obj.value
}
CImagePosition.prototype.onChange_preview_type = function(obj)
{
	if(obj.id.indexOf('icon') != -1) this.preview.src = arr_preview.icon
	if(obj.id.indexOf('thumb') != -1) this.preview.src = arr_preview.thumb
	if(obj.id.indexOf('orig') != -1) this.preview.src = arr_preview.orig
}