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
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class user_change_own_password_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'change_own_password';
  }

  function _define_actions()
  {
    return array(
      'change_own_password' => array(
          'action_path' => '/user/change_own_password_action',
          'template_path' => '/user/change_own_password.html',
          'action_name' => strings :: get('change_own_password', 'user'),
      ),
      'edit' => array(
          'popup' => true,
          'JIP' => true,
          'action_name' => strings :: get('edit'),
          'action_path' => '/site_object/edit_action',
          'template_path' => '/site_object/edit.html',
          'img_src' => '/shared/images/actions/edit.gif'
      ),
    );
  }
}

?>