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
require_once(LIMB_DIR . '/class/template/components/form/input_hidden_component.class.php');

class request_state_component extends input_hidden_component
{
  public function get_value()
  {
    $form = $this->find_parent_by_class('form_component');

    if($form->is_first_time())
    {
      if($value = Limb :: toolkit()->getRequest()->get($this->attributes['name']))
        return $value;
      else
        return '';
    }
    else
      return parent :: get_value();
  }
}
?>