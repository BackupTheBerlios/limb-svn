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

class users_folder_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/users_folder/admin_display.html'
        ),
        'create_user' => array(
            'template_path' => '/user/create.html',
            'action_path' => '/user/create_user_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.generic',
            'action_name' => strings :: get('create_user', 'user'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'icon' => 'edit'
        ),
    );
  }
}

?>