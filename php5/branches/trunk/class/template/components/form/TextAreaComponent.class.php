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
require_once(LIMB_DIR . '/class/template/components/form/ContainerFormElement.class.php');

class TextAreaComponent extends ContainerFormElement
{
  /**
  * Output the contents of the textarea, passing through htmlspecialchars().
  * Called from within a compiled template's render function
  */
  public function renderContents()
  {
    echo htmlspecialchars($this->getValue(), ENT_QUOTES);
  }
}

?>