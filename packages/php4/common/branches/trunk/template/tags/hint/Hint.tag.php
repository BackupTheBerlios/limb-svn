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

class HintHintTagInfo
{
  public $tag = 'hint';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'hint_hint_tag';
}

registerTag(new HintHintTagInfo());

class HintHintTag extends CompilerDirectiveTag
{
  public function preGenerate($code)
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
addEvent(document, 'mousemove', hint_mouse_move_handler);

function hintMouseMoveHandler(e)
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
    moveHint(current_hint_id, current_hint_x+10, current_hint_y+10);
}

function showHint(id)
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

function hideHint(id)
{
  if (dom) document.getElementById(id).style.visibility = "hidden";
  else if (ie4) document.all[id].style.visibility = "hidden";
  else if (nn4) document.hints[id].visibility = "hide";
}

function moveHint(id,x,y)
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

function startHint(id)
{
  if(current_hint_id)
    hideHint(current_hint_id);

  current_hint_id = id;

  moveHint(current_hint_id, current_hint_x+10, current_hint_y+10);
  showHint(current_hint_id);
}

function stop_hint()
{
  hideHint(current_hint_id);
  current_hint_id = null;
}
</script>
JS;
    parent :: preGenerate($code);

    if (!defined('HINT_SCRIPT_INCLUDED'))
    {
      $code->writeHtml($js);
      define('HINT_SCRIPT_INCLUDED', true);
    }
  }

  public function generateContents($code)
  {
    $link = $this->findChildByClass('hint_link_tag');
    $title = $this->findChildByClass('hint_title_tag');
    $content = $this->findChildByClass('hint_content_tag');

    $id = $this->getServerId();

    if($link &&  isset($link->attributes['class']))
      $hint_link_css = 'class="' . $link->attributes['class'] . '"';
    else
      $hint_link_css = 'class="hint-link-css"';

    if($link &&  isset($link->attributes['style']))
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

    if($title &&  isset($title->attributes['class']))
      $hint_title_css = 'class="' . $title->attributes['class'] . '"';
    else
      $hint_title_css = 'class="hint-title-css"';

    if($title &&  isset($title->attributes['style']))
      $hint_title_style = 'style="' . $title->attributes['style'] . '"';
    else
      $hint_title_style = '';

    if(isset($this->attributes['width']))
      $hint_width = $this->attributes['width'];
    else
      $hint_width = '250px';

    $code->writeHtml("<span {$hint_link_css} {$hint_link_style} onmouseover='this.style.cursor = \"default\"; startHint(\"{$id}\");' onmouseout='stop_hint();'>");

    if($link)
      $link->generate($code);
    else
      $code->writeHtml('<img src="/shared/images/i.gif">');

    $code->writeHtml("</span>");

    $code->writeHtml("
      <div id='{$id}' style='z-index: 1; position: absolute; visibility: hidden; width: {$hint_width};'>
      <table {$hint_table_css} {$hint_table_style} width='100%' cellpadding='6px' cellspacing='0'>");

    $code->writeHtml("<tr><td {$hint_title_css} {$hint_title_style} >");

    if($title)
      $title->generate($code);
    else
      $code->writeHtml("?");

    $code->writeHtml("</td></tr><tr><td>");

    if($content)
      $content->generate($code);
  }

  public function postGenerate($code)
  {
    $code->writeHtml('
      </td></tr>
      </table>
    </div>');

    parent :: postGenerate($code);
  }
}

?>