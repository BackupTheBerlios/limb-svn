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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class select_tag_info
{
  public $tag = 'select';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'select_tag';
}

register_tag(new select_tag_info());

/**
* Compile time component for building runtime select components
*/
class select_tag extends control_tag
{
  public function prepare()
  {
    if (array_key_exists('multiple', $this->attributes))
    {
      $this->attributes['multiple'] = 1;
      $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/select_multiple_component';
    }
    else
      $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/select_single_component';
  }

  /**
  * Ignore the compiler time contents and generate the contents at run time.
  */
  public function generate_contents($code)
  {
    if(isset($this->attributes['default_value']))
      $code->write_php($this->get_component_ref_code() . '->set_default_value("' . $this->attributes['default_value'] . '");');

    $code->write_php($this->get_component_ref_code() . '->render_contents();');

    parent :: generate_contents($code);
  }
}

?>