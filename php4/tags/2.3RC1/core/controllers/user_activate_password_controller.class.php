<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class user_activate_password_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'activate_password';
  }

  function _define_actions()
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