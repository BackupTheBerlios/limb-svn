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
require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class control_button_tag_info
{
  var $tag = 'control_button';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'control_button_tag';
}

register_tag(new control_button_tag_info());

/**
* Compile time component for button tags
*/
class control_button_tag extends control_tag
{
  var $runtime_component_path = '/core/template/components/form/control_button_component';

  function check_nesting_level()
  {
    if (!isset($this->attributes['action']))
    {
      error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'attribute' => 'action',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function get_rendered_tag()
  {
    return 'button';
  }

}

?>