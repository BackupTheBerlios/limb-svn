var active_tab = null

var LOADING_STATUS_PAGE = '/shared/loading.html';
var PROGRESS_PAGE = '/shared/progress.html';
var PROGRESS_IS_SHOWN = false;

if(get_query_item(location.href, 'popup'))
{
	add_event(window, 'load', process_popup);
}

function add_page_to_favourities()
{
	window.external.addFavorite(window.location, window.document.title);
}

function add_event(elm, evType, fn, useCapture) 
{ 
 if (elm.addEventListener)
 { 
   elm.addEventListener(evType, fn, useCapture); 
   return true; 
 } 
 else if (elm.attachEvent)
 { 
   var r = elm.attachEvent("on" + evType, fn); 
   return r; 
 } 
}  

function get_query_item(page_href, item_name)
{
	arr = get_query_items(page_href);
	
	if(arr[item_name])
		return arr[item_name];
	else
		return null;	
}

function build_query(items)
{
	query = '';
	for(index in items)
	{
		query = query + index + '=' + items[index] + '&';
	}
	return query;
}

function get_query_items(uri)
{
	query_items = new Array();
	
	arr = uri.split('?');
	if(!arr[1])
		return query_items;
		
	query = arr[1];

	arr = query.split('&');
	
	for(index in arr)
	{
		if(arr[index])
		{
			key_value = arr[index].split('=');
			if(!key_value[1])
				continue;
			
			query_items[key_value[0]] = key_value[1];
		}
	}
	
	return query_items;
}

function set_http_get_parameter(uri, parameter, val)
{
	uri_pieces = uri.split('?');
	
	items = get_query_items(uri);
	items[parameter] = val;
	
	return uri_pieces[0] + '?' + build_query(items);
}

function add_random_to_url(page_href)
{
	if(page_href.indexOf('?') == -1)
		page_href = page_href + '?';
		
	page_href = page_href.replace(/&*rn=[^&]+/g, '');
	
	items = page_href.split('#');
	
	page_href = items[0] + '&rn=' + Math.floor(Math.random()*10000);
	
	if(items[1])
		page_href = page_href + '#' + items[1];
			
	return page_href;
}

function toggle_display(obj_id)
{
	obj = document.getElementById(obj_id);
	if (typeof(obj) == 'object')
		if (typeof(obj.length) != 'undefined')
			for(i=0; i<obj.length; i++)
				toggle_obj_display(obj[i]);
		else
			toggle_obj_display(obj);
}

function toggle_obj_display(obj)
{
	if (obj.style.display == 'none')
	{
		obj.style.display = 'block';
		add_cookie_element('displayed_objects', obj.id);
		remove_cookie_element('hidden_objects', obj.id);
	}
	else
	{
		obj.style.display = 'none';
		add_cookie_element('hidden_objects', obj.id);
		remove_cookie_element('displayed_objects', obj.id);
	}
}

function optimize_window()
{	
	w = window;
	if(typeof(document.all['popup_div'])=='object')
	{
		var xper = 0.1
		var yper = 0.1
		var wper = 0.8
		var hper = 0.8
		w.moveTo(window.screen.width*xper, window.screen.height*yper);
		w.resizeTo(window.screen.width*wper, window.screen.height*hper);
		return
	}
	deltaX = w.document.body.scrollWidth - w.document.body.clientWidth;
	deltaY = w.document.body.scrollHeight - w.document.body.clientHeight;

	max_width = w.screen.width - 200;
	max_height = w.screen.height - 250;
	
	if (w.document.body.scrollWidth > max_width)
		deltaX = max_width - w.document.body.clientWidth;

	if (w.document.body.scrollHeight > max_height)
		deltaY = max_height - w.document.body.clientHeight;

	newLeft = (w.screen.width - w.document.body.clientWidth - deltaX)/2 - 20;
	newTop = (w.screen.height - w.document.body.clientHeight - deltaY)/2 - 30;

	w.moveTo(newLeft, newTop);

	w.resizeBy(deltaX, deltaY);
}

function process_popup()
{	
	href = location.href;

	optimize_window();
	
	if(opener && (get_query_item(href, 'reload_parent')))
		opener.location.reload();
}

function get_cookie(name)
{
  var a_cookie = document.cookie.split("; ");
  for (var i=0; i < a_cookie.length; i++)
  {
    var a_crumb = a_cookie[i].split("=");
    if (name == a_crumb[0]) 
      return unescape(a_crumb[1]);
  }
  return null;
}

function set_cookie(name, value, path, expires)
{
	path_str = (path) ? '; path=' + path : '; path=/';
	expires_str = (expires) ? '; expires=' + expires : '';
	
	cookie_str = name + '=' + value + path_str + expires_str;
	
  document.cookie = cookie_str;
}

function add_cookie_element(cookie_name, element)
{
	cookie_elements = get_cookie(cookie_name);
	if (cookie_elements == null || cookie_elements == 'undefined') 
		cookie_elements_array = new Array();
	else
		cookie_elements_array = cookie_elements.split(',');
	present = 0;
	for(i=0; i<cookie_elements_array.length; i++)
		if (cookie_elements_array[i] == element)
		{
			present = 1;
			break;
		}
	if (!present)
	{
		cookie_elements_array.push(element);
		new_cookie_elements = cookie_elements_array.join(',');
		set_cookie(cookie_name, new_cookie_elements);
	}
}

function remove_cookie_element(cookie_name, element)
{
	cookie_elements = get_cookie(cookie_name);
	if (cookie_elements == null || cookie_elements == 'undefined') 
		cookie_elements_array = new Array();
	else
		cookie_elements_array = cookie_elements.split(',');
	new_cookie_elements_array = new Array();
	present = 0;
	for(i=0; i<cookie_elements_array.length; i++)
		if (cookie_elements_array[i] != element)
			new_cookie_elements_array.push(cookie_elements_array[i]);
		else
			present = 1;
	if (present)
	{
		new_cookie_elements = new_cookie_elements_array.join(',');
		set_cookie(cookie_name, new_cookie_elements);
	}
}

//makes window w(current if not specified) go to href address
function jump(href, w)
{
	if(!w)
		w = window;

	w.location.href = LOADING_STATUS_PAGE;
	w.location.href = href;	
}

//makes window w(current if not specified) reload itself with new get request
function jump_change_get(get, w)
{
	href = document.location.href;
	is_get = href.indexOf('?');
	
	if(is_get > -1)
		href = href.substring(0, get_begin);
		
	jump(href + '?' + get, w);
}

//makes popup window at href address
function popup(href, window_name, window_params, dont_set_focus, on_close_handler, on_init_handler)
{	
	if (typeof(window_name) == 'undefined' || window_name == null) 
		window_name = '_generate';

	new_left = window.screen.width / 2 - 100;
	new_top = window.screen.height / 2 - 50;

	if (typeof(window_params) == 'undefined' || window_params == null) 
		window_params = 'width=150,height=50,left=' + new_left + ',top=' + new_top + ',scrollbars=yes,resizable=yes,help=no,status=yes';
	else
		window_params = 'left=' + new_left + ',top=' + new_top + ',' + window_params;
	
	if (window_name.toLowerCase() == '_generate')
		window_name = 'w' + hex_md5(href) + 's';

	if (typeof(window.popups) != 'array')
		window.popups = new Array();

	if (typeof(window.popups[window_name]) != 'array')
		window.popups[window_name] = new Array();
		
	if (typeof(on_close_handler) != 'undefined') 
	{		
		window.popups[window_name]['close_popup_handler'] = on_close_handler;
	}

	if (typeof(on_init_handler) != 'undefined') 
	{
		window.popups[window_name]['init_popup_handler'] = on_init_handler;
	}

	w = window.open(LOADING_STATUS_PAGE, window_name, window_params);
	
	if (href != LOADING_STATUS_PAGE)
		w.location.href = href;
	
	if(!dont_set_focus)
		w.focus();
		
	return w;
}

function get_close_popup_handler()
{
	return opener.popups[window.name]['close_popup_handler'];
}

function get_init_popup_handler()
{
	return opener.popups[window.name]['init_popup_handler'];
}

function show_progress()
{
	popup(PROGRESS_PAGE, 'progress', 'width=500,height=230,scrollbars=no,resizable=yes,help=no,status=no,menubar=no');
	PROGRESS_IS_SHOWN = true;
}

function click_href(href, window_name)
{
	has_progress = href.indexOf('progress=1');
	if(has_progress > -1)
		show_progress();

	is_popup = href.indexOf('popup=1');
	if(is_popup > -1)
		popup(href, window_name);
	
	return !((has_progress > -1) || (is_popup > -1));
}

function change_form_action(form_name, action)
{
	document.forms[form_name].action = action;
}

function add_form_action_parameter(form_name, parameter, val)
{
	action = document.forms[form_name].action;
	document.forms[form_name].action = set_http_get_parameter(action, parameter, val);
}

function add_form_hidden_parameter(form_name, parameter, val)
{
	if(document.forms[form_name])
	{
		action = document.createElement('INPUT');
		action.type = 'hidden';
		action.name = parameter;
		action.value = val;
		document.forms[form_name].appendChild(action);
	}
}

function submit_form(form_name, form_action)
{
	if(document.forms[form_name])
		form = document.forms[form_name];
	else
		return;
	
	has_progress = form_action.indexOf('progress=1');
	if(has_progress > -1)
		show_progress();
		
	is_popup = form_action.indexOf('popup=1');
	if(is_popup > -1)
	{
		window_name = 'w' + hex_md5(form_action) + 's';
		w = popup(LOADING_STATUS_PAGE, window_name);
		form.target = w.name;
	}

	form.action = form_action;
	form.submit();
}

function process_action_control(form_name, droplist_name)
{
	form = document.forms[form_name];
	droplist = form.elements[droplist_name];	
	
	if (typeof(droplist.value) != 'undefined')
		value = droplist.value;
	else
		value = droplist[0].value;
	
	submit_form(form_name, value);
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

function transfer_value(target_object_name, transfer_value)
{
	if(document.all[target_object_name])
	{
		obj = document.all[target_object_name];
		obj.value = transfer_value;
	}
}

function transfer_img_src(target_img_name, transfer_src)
{
	if(document.all[target_img_name])
	{
		obj = document.all[target_img_name];
		obj.src = transfer_src;
	}
}

function goto_page(message, href)
{
	if (confirm(message))
		jump(href)
}

function open_page(message, href, window_name, window_params)
{
	if (typeof(window_name) == 'undefined' || window_name == null)
		window_name = '_generate';

	if (typeof(window_params) == 'undefined' || window_params == null)
		window_name = 'height=400,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes';

	if (confirm(message))
		popup(href, window_name, window_params)
}

function bulk_options(start, end, selected, options_attrs)
{
	options = '';
	for(i = start; i <= end; i++)
		if (i == selected) options += '<option value=' + i + ' selected ' + options_attrs + '>'+i;
			else options += '<option value=' + i + ' ' + options_attrs + '>'+i;
	document.write(options)
}

//MD5 stuff 

var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}

function core_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;
  
  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;
 
    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);
  
}

function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

function core_hmac_md5(key, data)
{
  var bkey = str2binl(key);
  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++) 
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
  return core_md5(opad.concat(hash), 512 + 128);
}

function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}

function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}

function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}

function binl2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}
//
// [tabulator]
//

	function init_tab(name, tabulator_name, style_label, style_tabbox, eval_code)
	{
		var obj = document.getElementById(name)
		var tabulator = document.getElementById(tabulator_name)
		if(!ISSET(tabulator.tab_active)) tabulator.tab_active = document.getElementById(tabulator.attributes.getNamedItem('active').value)
		
		document.getElementById(obj.id+'_content').style.display = 'none'
		document.getElementById(tabulator.tab_active.id+'_content').style.display = 'block'
		
		obj.onmousedown = function()
		{	
			tabulator.tab_active.set_normal()
			this.set_active()
			tabulator.tab_active = this
		}
		obj.set_active = function()
		{
			document.getElementById(this.id+'_content').style.display = 'block'
			this.className = style_label + '-active'
			document.getElementById(this.id + '_box').className = style_tabbox + '-active'
			if(ISSET(eval_code))eval(eval_code)
		}
		obj.set_normal = function()
		{
			document.getElementById(this.id+'_content').style.display = 'none'
			this.className = style_label
			document.getElementById(this.id + '_box').className = style_tabbox
		}
	}
	
	function ISSET(obj)
	{
		return typeof(obj) != 'undefined'
	}
