<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class hint_hint_tag_info
{
  public $tag = 'hint';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_hint_tag';
}

register_tag(new hint_hint_tag_info());

class hint_hint_tag extends compiler_directive_tag
{
  public function pre_generate($code)
  {

$js = <<<JS
<script language="javascript">
dom = (document.getElementById) ? true : false;
nn4 = (document.hints) ? true : false;
ie = (document.all) ? true : false;
ie4 = ie && !dom;

var current_hint_x = -1, current_hint_y = -1;
var current_hint_id = null;

//document.onmousemove = hint_mouse_move_handler;
add_event(document, 'mousemove', hint_mouse_move_handler);

function hint_mouse_move_handler(e)
{
  if (dom)
  {
    current_hint_x = e.clientX;
    current_hint_y = e.clientY;
  }
  else if(ie4)
  {
    current_hint_x = e.x + document.body.scrollLeft;
    current_hint_y = e.y + document.body.scrollTop;
  }
  else if(nn4)
  {
    current_hint_x = e.pageX;
    current_hint_y = e.pageY;
  }

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

function move_hint(id,x,y)
{
  if (dom)
  {
    h = document.getElementById(id);
    h.style.left = x;
    h.style.top = y;
  }
  else if(ie4)
  {
    document.all[id].left = x;
    document.all[id].top = y;
  }
  else if(nn4)
  {
    document.hints[id].left = x;
    document.hints[id].top = y;
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
  current_hint_id = null;
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

  public function generate_contents($code)
  {
    $link = $this->find_child_by_class('hint_link_tag');
    $title = $this->find_child_by_class('hint_title_tag');
    $content = $this->find_child_by_class('hint_content_tag');

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

  public function post_generate($code)
  {
    $code->write_html('
      </td></tr>
      </table>
    </div>');

    parent :: post_generate($code);
  }
}

?>