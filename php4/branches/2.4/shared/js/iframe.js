onload_iframe = function()
{
	try{
	top.LAYOUT_CONTROL.onLoadPage()
	}catch(ex){}
}
add_event(window, 'load', onload_iframe)

/****************************************/
/*            resize layout             */
/****************************************/
function resize_content()
{
	try{
	var obj = document.getElementById('lo-place-center')
	obj.style.width = obj.parentNode.offsetWidth
	obj.style.height = document.body.offsetHeight
	obj.style.top = 0
	}catch(ex){}

	try{
	var left_obj = document.getElementById('lo-place-left').parentNode
	obj.style.left = left_obj.offsetWidth
	}catch(ex){}
	try{
	var right_obj = document.getElementById('lo-place-right').parentNode
	obj.style.right = right_obj.offsetWidth
	}catch(ex){}
}
document.resize_content = resize_content
add_event(window, 'load', 		resize_content)
add_event(window, 'resize', 		resize_content)
add_event(document, 'resize', 	resize_content)
	
	
	var arr_action_types = {
	'meta':				{'icon':'shared/images/icon/s/1.gif', 'title':'Мета-информация'},
	'details':			{'icon':'shared/images/icon/s/2.gif', 'title':'Детальная информация'},
	'create_document':{'icon':'shared/images/icon/s/3.gif', 'title':'Создать документ'},
	'edit_document':	{'icon':'shared/images/icon/s/4.gif', 'title':'Редактировать документ'},
	'publish':			{'icon':'shared/images/icon/s/5.gif', 'title':'Публиковать'},
	'unpublish':		{'icon':'shared/images/icon/s/6.gif', 'title':'Снять с публикации'},
	'delete':			{'icon':'shared/images/icon/s/7.gif', 'title':'Удалить'}
	}
	var arr_actions = {
					'id1':{'meta':'/root/meta/1','details':'/root/details/1'},
					'id2':{'meta':'/root/meta/2','details':'/root/details/2','create_document':'/root/create_document/2','edit_document':'/root/edit_document/2','publish':'/root/publish/2','unpublish':'/root/unpublish/2','delete':'/root/delete/2'},
					'id3':{'meta':'/root/meta/3','details':'/root/details/3','create_document':'/root/create_document/3','edit_document':'/root/edit_document/3','publish':'/root/publish/3','unpublish':'/root/unpublish/3','delete':'/root/delete/3'},
					'id4':{'meta':'/root/meta/4','details':'/root/details/4','create_document':'/root/create_document/4','edit_document':'/root/edit_document/4','publish':'/root/publish/4','unpublish':'/root/unpublish/4','delete':'/root/delete/4'},
					'id5':{'meta':'/root/meta/5','details':'/root/details/5','create_document':'/root/create_document/5','edit_document':'/root/edit_document/5','publish':'/root/publish/5','unpublish':'/root/unpublish/5','delete':'/root/delete/5'},
					'id6':{'meta':'/root/meta/6','details':'/root/details/6','create_document':'/root/create_document/6','edit_document':'/root/edit_document/6','publish':'/root/publish/6','unpublish':'/root/unpublish/6','delete':'/root/delete/6'},
					'id7':{'meta':'/root/meta/7','details':'/root/details/7','create_document':'/root/create_document/7','edit_document':'/root/edit_document/7','publish':'/root/publish/7','unpublish':'/root/unpublish/7','delete':'/root/delete/7'},
					'id8':{'meta':'/root/meta/8','details':'/root/details/8','create_document':'/root/create_document/8','edit_document':'/root/edit_document/8','publish':'/root/publish/8','unpublish':'/root/unpublish/8','delete':'/root/delete/8'},
					'id9':{'meta':'/root/meta/9','details':'/root/details/9','create_document':'/root/create_document/9','edit_document':'/root/edit_document/9','publish':'/root/publish/9','unpublish':'/root/unpublish/9','delete':'/root/delete/9'},
					'id10':{'meta':'/root/meta/10','details':'/root/details/10','create_document':'/root/create_document/10','edit_document':'/root/edit_document/10','publish':'/root/publish/10','unpublish':'/root/unpublish/10','delete':'/root/delete/10'},
					'id11':{'meta':'/root/meta/11','details':'/root/details/11','create_document':'/root/create_document/11','edit_document':'/root/edit_document/11','publish':'/root/publish/11','unpublish':'/root/unpublish/11','delete':'/root/delete/11'},
					'id12':{'meta':'/root/meta/12','details':'/root/details/12','edit_document':'/root/edit_document/12','publish':'/root/publish/12','unpublish':'/root/unpublish/12','delete':'/root/delete/12'},
					'id13':{'meta':'/root/meta/13','details':'/root/details/13','create_document':'/root/create_document/13','edit_document':'/root/edit_document/13','publish':'/root/publish/13','unpublish':'/root/unpublish/13','delete':'/root/delete/13'},
					'id14':{'meta':'/root/meta/14','details':'/root/details/14','create_document':'/root/create_document/14','edit_document':'/root/edit_document/14','publish':'/root/publish/14','unpublish':'/root/unpublish/14','delete':'/root/delete/14'},
					'id15':{'meta':'/root/meta/15','details':'/root/details/15','create_document':'/root/create_document/15','edit_document':'/root/edit_document/15','publish':'/root/publish/15','unpublish':'/root/unpublish/15','delete':'/root/delete/15'},
					'id16':{'meta':'/root/meta/16','details':'/root/details/16','create_document':'/root/create_document/16','edit_document':'/root/edit_document/16','publish':'/root/publish/16','unpublish':'/root/unpublish/16','delete':'/root/delete/16'},
					'id17':{'meta':'/root/meta/17','details':'/root/details/17','create_document':'/root/create_document/17','edit_document':'/root/edit_document/17','publish':'/root/publish/17','unpublish':'/root/unpublish/17','delete':'/root/delete/17'},
					'id18':{'meta':'/root/meta/18','details':'/root/details/18','create_document':'/root/create_document/18','edit_document':'/root/edit_document/18','publish':'/root/publish/18','unpublish':'/root/unpublish/18','delete':'/root/delete/18'},
					'id19':{'meta':'/root/meta/19','details':'/root/details/19','create_document':'/root/create_document/19','edit_document':'/root/edit_document/19','publish':'/root/publish/19','unpublish':'/root/unpublish/19','delete':'/root/delete/19'},
					'id20':{'meta':'/root/meta/20','details':'/root/details/20','create_document':'/root/create_document/20','edit_document':'/root/edit_document/20','publish':'/root/publish/20','unpublish':'/root/unpublish/20','delete':'/root/delete/20'},
					'id21':{'meta':'/root/meta/21','details':'/root/details/21','create_document':'/root/create_document/21','edit_document':'/root/edit_document/21','publish':'/root/publish/21','unpublish':'/root/unpublish/21','delete':'/root/delete/21'},
					'id22':{'meta':'/root/meta/22','details':'/root/details/22','create_document':'/root/create_document/22','edit_document':'/root/edit_document/22','publish':'/root/publish/22','unpublish':'/root/unpublish/22','delete':'/root/delete/22'},
					'id23':{'meta':'/root/meta/23','details':'/root/details/23','create_document':'/root/create_document/23','edit_document':'/root/edit_document/23','publish':'/root/publish/23','unpublish':'/root/unpublish/23','delete':'/root/delete/23'},
					'id24':{'meta':'/root/meta/24','details':'/root/details/24','create_document':'/root/create_document/24','edit_document':'/root/edit_document/24','publish':'/root/publish/24','unpublish':'/root/unpublish/24','delete':'/root/delete/24'}
					}


function initFileUploads()
{
	var arr = document.getElementsByTagName('button')
	for(var i=0; i<arr.length; i++)
	{
		if(arr[i].className != 'file') continue
		
		var input = document.getElementById( arr[i].id + '_input')
		var fake_file = input.fake_file
		if(!fake_file)  fake_file = document.createElement('input')
		fake_file.type = 'file'
		fake_file.size = 1
		fake_file.className = 'file'
		fake_file.style.position = 'absolute'
			
		arr[i].parentNode.appendChild(fake_file)
		fake_file.style.left = get_real_offset(arr[i], 'left', true) - (fake_file.offsetWidth - arr[i].offsetWidth)
		fake_file.style.top = get_real_offset(arr[i], 'top', true)
		input.fake_file = fake_file
		fake_file.input = input
		fake_file.onchange = function()
		{
			this.input.value = this.value
			if(this.input.onchange) this.input.onchange()
		}

		var clear = document.getElementById( arr[i].id + '_clear')
		if(!clear) continue
		clear.input = input
		clear.fake_file = fake_file
		clear.onclick = function()
		{
			this.input.value = ''
		}
	}
	
}
/*<!--END:[ fileopen ]-->*/

add_event(window, 'load', initFileUploads)


function get_filename(path)
{
	var arr = path.split('\\')
	var fn = arr[arr.length-1]
	
	return fn
}
