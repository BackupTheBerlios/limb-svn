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
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class SelectTagInfo
{
  public $tag = 'select';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'select_tag';
}

registerTag(new SelectTagInfo());

/**
* Compile time component for building runtime select components
*/
class SelectTag extends ControlTag
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
  public function generateContents($code)
  {
    if(isset($this->attributes['default_value']))
      $code->writePhp($this->getComponentRefCode() . '->set_default_value("' . $this->attributes['default_value'] . '");');

    $code->writePhp($this->getComponentRefCode() . '->render_contents();');

    parent :: generateContents($code);
  }
}

?>