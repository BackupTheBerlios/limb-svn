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

class select_tag_info
{
  var $tag = 'select';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'select_tag';
}

register_tag(new select_tag_info());

/**
* Compile time component for building runtime select components
*/
class select_tag extends control_tag
{
  var $runtime_component_path;

  /**
  *
  * @return void
  * @access protected
  */
  function prepare()
  {
    if (array_key_exists('multiple', $this->attributes))
    {
      $this->attributes['multiple'] = 1;
      $this->runtime_component_path = '/core/template/components/form/select_multiple_component';
    }
    else
      $this->runtime_component_path = '/core/template/components/form/select_single_component';
  }

  /**
  * Ignore the compiler time contents and generate the contents at run time.
  *
  * @return void
  * @access protected
  */
  // Ignore the compiler time contents and generate the contents at run time.
  function generate_contents(&$code)
  {
    if(isset($this->attributes['default_value']))
      $code->write_php($this->get_component_ref_code() . '->set_default_value("' . $this->attributes['default_value'] . '");');

    $code->write_php($this->get_component_ref_code() . '->render_contents();');

    parent :: generate_contents($code);
  }
}

?>