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


require_once(LIMB_DIR . '/core/template/components/form/container_form_element.class.php');

class text_area_component extends container_form_element
{
  /**
  * Output the contents of the textarea, passing through htmlspecialchars().
  * Called from within a compiled template's render function
  *
  * @return void
  * @access protected
  */
  function render_contents()
  {
    echo htmlspecialchars($this->get_value(), ENT_QUOTES);
  }
}

?>