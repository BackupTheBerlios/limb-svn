<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: locale.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

class hint_hint_tag_info
{
	var $tag = 'hint';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'hint_hint_tag';
} 

register_tag(new hint_hint_tag_info());

class hint_hint_tag extends compiler_directive_tag
{
	function pre_generate(&$code)
	{
	
$js = <<<JS
<script language="javascript">
dom = (document.getElementById) ? true : false;
nn4 = (document.hints) ? true : false;
ie = (document.all) ? true : false;
ie4 = ie && !dom;

var current_hint_x = -1, current_hint_y = -1;
var mouse_moved = 0;
var current_hint_id = "";

document.onmousemove = hint_mouse_move_handler; 

function hint_mouse_move_handler(e)
{
  current_hint_x = (nn4) ? (e.pageX):(event.x + document.body.scrollLeft);
  current_hint_y = (nn4) ? (e.pageY):(event.y + document.body.scrollTop);
  mouse_moved++;
  on_hint_mouse_move();
}

function on_hint_mouse_move() 
{
  if(current_hint_id)
  	move_hint(current_hint_id, current_hint_x+10, current_hint_y+10);
}

function show_hint(id) 
{
  if (dom) 
  {
  	document.getElementById(id).style.visibility = "visible";
   	document.getElementById(id).style.display = '';
  }
  else if (ie4) 
  {
  	document.all[id].style.visibility = "visible";
    document.all[id].style.display= '';
  } 
  else if (nn4) 
  {
  	document.hints[id].visibility = "show";
    document.hints[id].display='';
  }
}

function hide_hint(id) 
{
  if (dom) document.getElementById(id).style.visibility = "hidden";
  else if (ie4) document.all[id].style.visibility = "hidden";
  else if (nn4) document.hints[id].visibility = "hide";
}

function move_hint(idname,x,y)
{
  if (dom)
  {
    with(eval(idname))
    {
      style.left = x;
      style.top = y;
    }
  }
  else if(nn4)
  {
    document.hints[idname].left = x;
    document.hints[idname].top = y;
  }
}

function start_hint(id)
{
  if(current_hint_id)
  	hide_hint(current_hint_id);
  	
  current_hint_id = id;
  
  move_hint(current_hint_id, current_hint_x+10, current_hint_y+10);
  show_hint(current_hint_id);
}

function stop_hint()
{
  hide_hint(current_hint_id);
  current_hint_id = "";
}
</script>
JS;

		parent :: pre_generate($code);
		
		if (!defined('HINT_SCRIPT_INCLUDED'))
		{
			$code->write_html($js);
			define('HINT_SCRIPT_INCLUDED', true);
		}
	} 
	
	function generate_contents(&$code)
	{
		$link =& $this->find_child_by_class('hint_link_tag');
		$title =& $this->find_child_by_class('hint_title_tag');
		$content =& $this->find_child_by_class('hint_content_tag');

		$id = $this->get_server_id();

		if($link && isset($link->attributes['class']))
			$hint_link_css = 'class="' . $link->attributes['class'] . '"';
		else
			$hint_link_css = 'class="hint-link-css"';

		if($link && isset($link->attributes['style']))
			$hint_link_style = 'style="' . $link->attributes['style'] . '"';
		else
			$hint_link_style = '';

		if(isset($this->attributes['class']))
			$hint_table_css = 'class="' . $this->attributes['class'] . '"';
		else
			$hint_table_css = 'class="hint-table-css"';

		if(isset($this->attributes['style']))
			$hint_table_style = 'style="' . $this->attributes['style'] . '"';
		else
			$hint_table_style = '';	

		if($title && isset($title->attributes['class']))
			$hint_title_css = 'class="' . $title->attributes['class'] . '"';
		else
			$hint_title_css = 'class="hint-title-css"';	
			
		if($title && isset($title->attributes['style']))
			$hint_title_style = 'style="' . $title->attributes['style'] . '"';
		else
			$hint_title_style = '';

		if(isset($this->attributes['width']))
			$hint_width = $this->attributes['width'];
		else
			$hint_width = '250px';
		
		$code->write_html("<span {$hint_link_css} {$hint_link_style} onmouseover='this.style.cursor = \"default\"; start_hint(\"{$id}\");' onmouseout='stop_hint();'>");
		
		if($link)
			$link->generate($code);
		else
			$code->write_html('<img src="/shared/images/i.gif">');
		
		$code->write_html("</span>");
			
		$code->write_html("
			<div id='{$id}' style='z-index: 1; position: absolute; visibility: hidden; width: {$hint_width};'>
			<table {$hint_table_css} {$hint_table_style} width='100%' cellpadding='6px' cellspacing='0'>");
		
		$code->write_html("<tr><td {$hint_title_css} {$hint_title_style} >");
		
		if($title)
			$title->generate($code);
		else
			$code->write_html("?");
		
		$code->write_html("</td></tr><tr><td>");
		
		if($content)
			$content->generate($code);
	}

	function post_generate(&$code)
	{
		$code->write_html('
			</td></tr>
			</table>
		</div>');
		
		parent::post_generate($code);
	} 
	
} 

?>