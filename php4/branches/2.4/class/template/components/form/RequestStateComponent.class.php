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
require_once(LIMB_DIR . '/class/template/components/form/InputHiddenComponent.class.php');

class RequestStateComponent extends InputHiddenComponent
{
  function getValue()
  {
    $form = $this->findParentByClass('form_component');

    if($form->isFirstTime())
    {
      $toolkit =& Limb :: toolkit();
      $request =& $toolkit->getRequest();
      if($value = $request->get($this->attributes['name']))
        return $value;
      else
        return '';
    }
    else
      return parent :: getValue();
  }
}
?>