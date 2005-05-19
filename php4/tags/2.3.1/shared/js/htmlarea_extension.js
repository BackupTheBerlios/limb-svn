function install_limb_full_extension(config)
{
  upper_toolbar = [ "fontname", "space",
      "fontsize", "space",
      "formatblock", "space",
      "bold", "italic", "underline", "strikethrough", "separator",
      "subscript", "superscript", "separator",
      "copy", "cut", "paste", "space"];

  if(HTMLArea.is_gecko)
  {
    upper_toolbar[upper_toolbar.length] = "undo";
    upper_toolbar[upper_toolbar.length] = "redo";
  }

  config.toolbar =
  [
    upper_toolbar,

    [ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
      "lefttoright", "righttoleft", "separator",
      "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
      "forecolor", "hilitecolor", "separator",
      "inserthorizontalrule", "createlink", "insertimage", "inserttable", "insertlimbimage", "insertlimbfile",  "clear_msw", "htmlmode", "separator",
      "about" ]
  ];

  register_limb_buttons(config);
  register_css(config);
}

function install_limb_lite_extension(config)
{
  config.toolbar = [
      [ 'bold', 'italic', 'underline', 'strikethrough', 'separator',
        'subscript', 'superscript', 'separator',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'separator',
        'insertorderedlist', 'insertunorderedlist', 'outdent', 'indent', 'separator',
        'copy', 'cut', 'paste','separator',
        "inserthorizontalrule", "createlink", "insertimage", "inserttable", "insertlimbimage", "insertlimbfile",  "clear_msw", "htmlmode", "separator"
      ]
    ];

  register_limb_buttons(config);
  register_css(config);
}

function register_limb_buttons(config)
{
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
    image     : "/shared/images/editor/ed_link_file.gif",
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

function register_css(config)
{
  if(window.RICHEDIT_IMPORT_CSS)
    config.pageStyle = "@import url(" + RICHEDIT_IMPORT_CSS + ");";
  else
    config.pageStyle = "@import url(/design/main/styles/main.css);";
}

function insert_limb_repository_image(e, id)
{
  var editor = e;
  popup("/root/tools/image_select?popup=1", null, null, false,

    function(image)
    {
      if (!image)
        return;

      if(HTMLArea.is_gecko)
      {
        parent = editor.getParentElement();

        if (parent.tagName == 'IMG')
          editor.execCommand('Delete');

        selection = editor._getSelection();
        range = editor._createRange(selection);

        var img_element = editor._doc.createElement("IMG");

        img_element.src = '/root?node_id=' + image['node_id'] + '&' + image['type'];
        img_element.setAttribute('limb_attributes', image['node_id'] + ':' + image['type'] + ':' + image['link_to']);
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

        if (image['link_to'])
        {
          var a = editor._doc.createElement("A");

          a.href = '/root?node_id=' + image['node_id'] + '&' + image['link_to'];
          a.target = '_blank';
          a.appendChild(img_element);
          node = a;
        }
        else
          node = img_element;

        editor.insertNodeAtSelection(node);
      }
      else//IE dirty hack!
      {
        var range = editor._doc.selection.createRange();

        // delete selected content and replace with image
        if (editor._doc.selection.type == "Control")
        {
          range.execCommand('Delete');
          range = editor._doc.selection.createRange();
        }
        if (image['link_to'])
        {
          idstr = "556e697175657e537472696e67";
          range.execCommand("CreateLink", null, idstr);
          coll = editor._doc.getElementsByTagName("A");
          for(i=0; i<coll.length; i++)
          {
            if (coll[i].href == idstr)
            {
              link_element = coll[i];
              link_element.href = '/root?node_id=' + image['node_id'] + '&' + image['link_to'];
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

        img_element.setAttribute('limb_attributes', image['node_id'] + ':' + image['type'] + ':' + image['link_to']);
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
      }
    }
    ,
    function()
    {
      sel = editor.getParentElement();

      if (sel.tagName == 'IMG' && sel.getAttribute('limb_attributes'))
      {
        params = sel.getAttribute('limb_attributes');
        params = params.split(':');
        img = {node_id: params[0],
               width:   sel.width,
               height:  sel.height,
               border:  sel.border,
               hspace:  sel.hspace,
               vspace:  sel.vspace,
               align:   sel.align,
               alt:     sel.alt,
               type:    params[1],
               link_to: params[2]};
        return img;
      }
      else
      {
        return null;
      }
    }
  );
}

function insert_limb_repository_file(e, id)
{
  var editor = e;

  popup("/root/tools/file_select?popup=1", null, null, false,

    function (file)
    {
      editor._doc.execCommand("createlink", false, '/root?node_id=' + file['node_id']);
      a = editor.getParentElement();
      var sel = editor._getSelection();
      var range = editor._createRange(sel);
      if (!HTMLArea.is_ie)
      {
        a = range.startContainer;
        if (!/^a$/i.test(a.tagName))
          a = a.nextSibling;
      }

      if(a)
        a.title = file['title'] + ' : ' + file['size'] + ' bytes';

    },
    function (obj){});
}

//===========================================================

prevGetHTML = HTMLArea.getHTML;
HTMLArea.getHTML = function(root, outputRoot, editor)
{
  res = prevGetHTML(root, outputRoot, editor);

  if(outputRoot == false && trim(res) == '<br />')
    return '';

  return res;
}

HTMLArea.prototype.stripBaseURL = function(string) {
  var baseurl = this.config.baseURL;

  // strip to last directory in case baseurl points to a file
  baseurl = baseurl.replace(/(\/\/[^\/]+)(\/?.*)$/, '$1');
  var basere = new RegExp(baseurl);
  string = string.replace(basere, "");

  // strip host-part of URL which is added by MSIE to links relative to server root
  baseurl = baseurl.replace(/^(https?:\/\/[^\/]+)(.*)$/, '$1');
  basere = new RegExp(baseurl);
  return string.replace(basere, "");
};

HTMLArea.isBlockElement = function(el) //quick and dirty hack
{
  if(!el.tagName)//sometimes it can be not a tag at all in Mozilla!
  {
    return false;
  }

  var blockTags = " body form textarea fieldset ul ol dl li div " +
    "p h1 h2 h3 h4 h5 h6 quote pre table thead " +
    "tbody tfoot tr td iframe address ";

  return (blockTags.indexOf(" " + el.tagName.toLowerCase() + " ") != -1);
};

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