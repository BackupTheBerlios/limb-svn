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

class user_activate_password_controller extends site_object_controller
{
  protected function _define_default_action()
  {
    return 'activate_password';
  }

  protected function _define_actions()
  {
    return array(
        'activate_password' => array(
          'action_path' => '/user/activate_password_action',
          'template_path' => '/user/activate_password.html',
          'action_name' => strings :: get('activate_password', 'user'),
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
    );
  }
}

?>