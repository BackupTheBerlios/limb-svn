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
require_once(LIMB_DIR . '/class/template/components/form/input_form_element.class.php');

class input_file_component extends input_form_element
{
  /**
  * We can't get a meaningful 'value' attribute for file upload controls
  * after form submission - the value would need to be the full path to the
  * file on the client machine and we don't have a handle on that
  * information. The component's 'value' is instead set to the relevant
  * portion of the $_FILES array, allowing initial validation of uploaded
  * files.
  */
  public function get_value()
  {
    return;
  }
}
?>