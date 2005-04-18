function var_dump(obj, level)
{
	if(level>10) return '';

	var res = '';
	var shift = "";
	
	for(i=0; i<level*2; i++) 
		shift+="\t\t";
	
	if(typeof(obj)=='object')
	{
		try
		{
			for(key in obj)
			{
				if(obj[key] == 'none' || obj[key] == '' || obj[key]==null) continue;
					
				if(typeof(obj[key]) == 'unknown' || typeof(obj[key]) == 'undefined') continue;
				
				res += shift + key + ' = ' + obj[key] + '\n';
				res += var_dump(obj[key], level++);
			}
		}
		catch(e){}
	}
	return res;
}

function trace(txt, wnd)
{
  if(!wnd) wnd = window
  var elem=wnd.document.createElement("SPAN")
  wnd.document.body.appendChild(elem)
  elem.innerHTML = txt
}
function get_path(obj)
{
	var str=''
	var arr = new Array()
	while(obj.parentNode != null)
	{
		if(obj == this)return true
		arr[arr.length] = obj
		obj = obj.parentNode
	}
	arr.reverse()
	for(var i=0;i<arr.length;i++)
		str += '' + arr[i].tagName + (arr[i].className ? ' class=' + arr[i].className : '') + (arr[i].id ? ' class=' + arr[i].id : '') + ' -->'
//		str += '<' + arr[i].tagName + (arr[i].className ? ' class=' + arr[i].className : '') + (arr[i].id ? ' class=' + arr[i].id : '') + '>'
//		str += '<' + arr[i].tagName + '>\n'
	return str
}


//
// debug-info
//
function debug_info(){}
debug_info.prototype.onclick = function()
{
	WS = new ActiveXObject("WScript.shell");
	WS.exec("uedit32.exe " + this.alt);
}
//
// [debug]
//

function onload_iframe()
{
	show_hide_debug_info_all_frames(0)
}
debug_window_onload = window.onload
window.onload = function()
{
	show_hide_debug_info_all_frames(0)
	if(debug_window_onload) debug_window_onload()
}
debug_document_onkeydown = document.onkeydown
document.onkeydown = function(evnt)
{
  if(!evnt) evnt = event
	if(evnt.shiftKey)
	if(evnt.keyCode == 192)
	{
		if(typeof(is_debug_info_enabled)=='undefined') is_debug_info_enabled = 1
		show_hide_debug_info_all_frames(is_debug_info_enabled)
		is_debug_info_enabled = !is_debug_info_enabled
	}
	if(evnt.keyCode == 113)
	  if(top.reload)top.reload(top.active_tab)
	  
	if(debug_document_onkeydown) debug_document_onkeydown()
}
function show_hide_debug_info_all_frames(is_show)
{
	show_hide_debug_info(document, is_show)
	var arr = top.window.frames
	for(var i=0; i<arr.length; i++)
		show_hide_debug_info(arr[i].document, is_show)
}
function show_hide_debug_info(doc, is_show)
{
	var arr = doc.getElementsByTagName('DIV')
	for(var i=0; i<arr.length; i++)
	{
		if(arr[i].className.indexOf('debug-')!=-1)
		{
			if(!arr[i].classNameOld) arr[i].classNameOld = arr[i].className
			
			arr[i].className = is_show ? arr[i].classNameOld : 'debug-empty'
		}
	}
	arr = doc.getElementsByTagName('IMG')
	for(var i=0; i<arr.length; i++)
		if(arr[i].className.indexOf('debug-')!=-1)
			arr[i].style.display = is_show ? 'block' : 'none'
}

function show_included()
{
	var arr = document.styleSheets
	var str ="<b>"+window.location+"</b><br>"
	str += "csses<br>"
	for(var i=0; i<arr.length; i++)
	{
	  var path = get_http_path(arr[i].href)
		str += "<a href='javascript:void(0)' class='debug-info-img' alt='" + path + "'>" + path + '</a><br>'
	}
	var arr = document.getElementsByTagName('SCRIPT')
	str += "scripts<br>"
	for(var i=0; i<arr.length; i++)
	{
	  var path = get_http_path(arr[i].src)
		if(arr[i].src != '')
		str += "<a href='javascript:void(0)' class='debug-info-img' alt='" + path + "'>" + path + '</a><br>'
		
	}
	trace(str, top)
}
function get_http_path(href)
{
	var path
	if(href.indexOf('shared')!=-1)
	  path = top.HTTP_SHARED_DIR + href.substr(href.indexOf('shared') + 7)
	else
	  path = top.LOCAL_DESIGN_DIR + href.substr(href.indexOf('design') + 7)
	return path
}

tmp_window_onload = window.onload
window.onload = function()
{
//	show_included()

	if(window != top)
		if(top.onload_iframe)
			top.onload_iframe()
	if(tmp_window_onload) tmp_window_onload()
}
