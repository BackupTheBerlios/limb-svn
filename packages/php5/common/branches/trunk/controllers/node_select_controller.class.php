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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

class node_select_controller extends site_object_controller
{
  protected function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/node_select/display.html',
            'popup' => true,
        ),
    );
  }
}

?>