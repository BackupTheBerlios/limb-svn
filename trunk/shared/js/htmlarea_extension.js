function install_limb_full_extension(config)
{
  config.toolbar = 
  [
		[ "fontname", "space",
		  "fontsize", "space",
		  "formatblock", "space",
		  "bold", "italic", "underline", "strikethrough", "separator",
		  "subscript", "superscript", "separator",
		  "copy", "cut", "paste", "space", "undo", "redo" ],

		[ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
		  "lefttoright", "righttoleft", "separator",
		  "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
		  "forecolor", "hilitecolor", "separator",
		  "inserthorizontalrule", "createlink", "insertimage", "inserttable", "insertlimbimage", "insertlimbfile",  "clear_msw", "htmlmode", "separator",
		  "about" ]
	];
	
  config.registerButton({
    id        : "insertlimbimage",
    tooltip   : "Insert image from repository",
    image     : "/shared/images/editor/ed_image.gif",
    textMode  : false,
    action    : insert_limb_repository_image
  });	

  config.registerButton({
    id        : "insertlimbfile",
    tooltip   : "Insert file from repository",
    image     : "/shared/images/editor/ed_link.gif",
    textMode  : false,
    action    : insert_limb_repository_file
  });	

  config.registerButton({
    id        : "clear_msw",
    tooltip   : "Clean MS formatting",
    image     : "/shared/images/editor/ed_clear_msword.gif",
    textMode  : false,
    action    : function(editor, id) {
                  alert('implement me!');
                }
  });	  
}

function insert_limb_repository_image(e, id)
{
  var editor = e;
  
	popup("/root/image_select", null, null, false,  
	
    function(image) 
    {
    	if (!image)
    		return false;
    		
			var sel = editor._getSelection();
			var range = editor._createRange(sel);    		
    	      
      // delete selected content and replace with image
      if (sel.type == "Control")
      {
        range.execCommand('Delete');
        range = editor._createRange(sel);
      }
      
      link_to = image['link_to'];
      if (link_to.length > 1)
      {
      	idstr = "556e697175657e537472696e67";
    	  range.execCommand("CreateLink", null, idstr);
    	  coll = editor._doc.getElementsByTagName("A");
    	  for(i=0; i<coll.length; i++)
    	  {
    	  	if (coll[i].href == idstr)
    	  	{
    			  link_element = coll[i];
    			  link_element.href = '/root?node_id=' + image['node_id'] + '&' + link_to;
    			  link_element.target = '_blank';
    			  img_element = editor._doc.createElement("IMG");
    			  img_element.src = '/root?node_id=' + image['node_id'] + '&' + image['type'];
    			  link_element.appendChild(img_element);
    			}
    		}
    	}
      else
    	{
    		idstr = "\" id=\"556e697175657e537472696e67";
    	  range.execCommand("InsertImage", null, idstr);
    	  img_element = editor._doc.all['556e697175657e537472696e67'];
    	  img_element.removeAttribute("id");
    	  img_element.src = '/root?node_id=' + image['node_id'] + '&' + image['type'];
    	}
    	
      img_element.id = image['node_id'] + ':' + image['type'] + ':' + image['link_to'];
      img_element.border = image['border'];
      img_element.alt = image['alt'];
      img_element.align = image['align'];
      img_element.hspace = image['hspace'];
      img_element.vspace = image['vspace'];
      new_width = parseInt(image['width']);
      new_height = parseInt(image['height']);
      if (new_width != 0 && !isNaN(new_width))
      	img_element.width = image['width'];
      if (new_height != 0 && !isNaN(new_height))
      	img_element.height = image['height'];
      
      range.collapse(false);
      range.select();
    }	  
	  ,
    function()
    {      
			var sel = editor._getSelection();
			var range = editor._createRange(sel);    		
    
      // delete selected content and replace with image
      if (sel.type == "Control")
      {
      	if (range.item(0).tagName == 'IMG')
      	{
      		params = range.item(0).id;
      		params = params.split(':');
    			img = {node_id:  params[0],
    						 width:   range.item(0).width,
    						 height:   range.item(0).height,
    						 border:  range.item(0).border,
    						 hspace:  range.item(0).hspace,
    						 vspace:  range.item(0).vspace,
    						 align:  range.item(0).align,
    						 alt:  range.item(0).alt,
    						 type:  params[1],
    						 link_to: params[2]};
    			return img;
      	}
      }
      return false
    }
	);
}

function insert_limb_repository_file(e, id)
{
  var editor = e;
  
	popup("/root/file_select", null, null, false, 
	
  	function (file)
    {
      var htmlSelectionControl = "Control";
      var grngMaster = editor._doc.selection.createRange();
      
      // delete selected content and replace with image
      if (editor._doc.selection.type == htmlSelectionControl)
      {
        grngMaster.execCommand('Delete');
        grngMaster = editor._doc.selection.createRange();
      }
        
    	idstr = "556e697175657e537472696e67";
      grngMaster.execCommand("CreateLink", null, idstr);
      coll = editor._doc.getElementsByTagName("A");
      for(i=0; i<coll.length; i++)
      {
      	if (coll[i].href == idstr)
      	{
    		  link_element = coll[i];
      		if (link_element.firstChild == null)
      		{
      			txt = editor._doc.createTextNode(file.name);
      			link_element.appendChild(txt);
      		}
    		  link_element.href = '/root?node_id=' + file.node_id;
    		  link_element.title = file.name;
    		}
    	}
    
      grngMaster.collapse(false);
      grngMaster.select();
    }, 
	  function (obj){});
}

/*function install_limb_lite_extension(config)
{		
  config.toolbar = [
			[ 'bold', 'italic', 'underline', 'strikethrough', 'separator',
			  'subscript', 'superscript', 'separator',
			  'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'separator',
				'insertorderedlist', 'insertunorderedlist', 'outdent', 'indent', 'separator',
				'copy', 'cut', 'paste','separator',
			  'inserthorizontalrule', 'createlink', 'insertimage', 'insertlinkfile', 'htmlmode', 'separator',
			  'popupeditor', 'separator', 'clear_msw'
			]
		];
}*/


function clear_msw(editor)
{
	clear_empty_tags(editor);
	clear_format(editor);
}

function clear_format(editor)
{
	var arr = editor._doc.body.all
	for(v in arr)
		if(typeof(arr[v]) == 'object')
			arr[v].clearAttributes()
}

function clear_styles(editor)
{
	var arr = editor._doc.body.all
	for(v in arr)
	{
		if(typeof(arr[v]) == 'object')
		{
			arr[v].removeAttribute("className")
			arr[v].removeAttribute("style")
		}
	}
}
function clear_empty_tags(editor)
{
	var arr = editor._doc.body.all
	do
	{
  	var s = 0
	  for(v in arr)
    {
		  if(typeof(arr[v]) == 'object')
		  {
			  var obj = arr[v]
			  var str = obj.innerText
			  str = str.replace(" ",'')
			  str = str.replace("\n",'')
			  str = str.replace("\t",'')
			  try{
  			  if(str == '')
			    {
				    arr[v].removeNode()
				    s++
			    }
			  }catch(ex){}
		 }
  	}
	}while(s > 0)
}
function is_non_pair_tag(name)
{	
	var exceptions = ['img', 'br', 'p', 'li', 'option', 'input', 'textarea']
	for(v in arr)
	{
		if(arr[v] == name)
		  return true;
	}
	
	return false;
}