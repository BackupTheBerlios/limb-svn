<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
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
    parent :: pre_generate($code);

    if (!defined('HINT_SCRIPT_INCLUDED'))
    {
      $code->write_html("<script type='text/javascript' src='/shared/js/hint.js'></script>");
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