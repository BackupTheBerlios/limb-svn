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
  public function getValue()
  {
    $form = $this->findParentByClass('form_component');

    if($form->isFirstTime())
    {
      if($value = Limb :: toolkit()->getRequest()->get($this->attributes['name']))
        return $value;
      else
        return '';
    }
    else
      return parent :: getValue();
  }
}
?>