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
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class login_object_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'login';
  }

  function _define_actions()
  {
    return array(
        'login' => array(
            'action_path' => 'login_action',
            'template_path' => 'login.html'
        ),
        'logout' => array(
            'action_path' => 'logout_action',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'change_user_locale' => array(
            'popup' => true,
            'action_name' => strings :: get('change_locale', 'user'),
            'action_path' => '/user/change_user_locale_action',
        ),
    );
  }
}

?>