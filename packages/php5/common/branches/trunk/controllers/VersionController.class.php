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

class version_controller extends site_object_controller
{
  protected function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/version/display.html',
        ),
        'recover' => array(
            'action_path' => '/version/recover_version_action',
            'popup' => true
        )
    );
  }
}

?>