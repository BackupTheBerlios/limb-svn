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

class tab_item_label_tag_info
{
  var $tag = 'tab_item:label';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'tab_item_label_tag';
}

register_tag(new tab_item_label_tag_info());

class tab_item_label_tag extends compiler_directive_tag
{
  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if (!is_a($this->parent, 'tabs_labels_tag'))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('tag' => $this->tag,
          'enclosing_tag' => 'tabs:labels',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!isset($this->attributes['tab_id']))
    {
      error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('tag' => $this->tag,
          'attribute' => 'tab_id',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->parent->parent->tabs[] = $this->attributes['tab_id'];
  }

  function pre_generate(&$code)
  {
    $id = $this->attributes['tab_id'];

    $code->write_html("<td id={$id}>
          <table border='0' cellspacing='0' cellpadding='0' style='height:100%'>
          <tr>
            <td nowrap {$this->parent->parent->tab_class}><a href='JavaScript:void(0);'>");

    parent :: pre_generate($code);
  }

  function post_generate(&$code)
  {
    $code->write_html("</a></td>
          </tr>
          </table>
        </td>
    ");

    parent :: post_generate($code);
  }

}

?>