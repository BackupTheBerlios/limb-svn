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
require_once(LIMB_DIR . '/class/template/components/form/OptionsFormElement.class.php');

class SelectMultipleComponent extends OptionsFormElement
{
  protected function _processNameAttribute($value)
  {
    return parent :: _processNameAttribute($value) . '[]';
  }

  protected function _renderOptions()
  {
    $values = $this->getValue();

    if(!is_array($values))
      $values = array();

    foreach($this->choice_list as $key => $contents)
    {
      $this->option_renderer->renderAttribute($key, $contents, in_array($key, $values));
    }
  }

  public function getValue()
  {
    return ContainerFormElement :: getValue();
  }
}
?>