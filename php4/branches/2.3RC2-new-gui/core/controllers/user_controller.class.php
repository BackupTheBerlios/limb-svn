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

class user_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/user/admin_display.html',
        ),
        'edit' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('edit_user', 'user'),
            'action_path' => '/user/edit_user_action',
            'template_path' => '/user/edit.html',
            'img_src' => '/shared/images/actions/edit.gif'
        ),
        'set_membership' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('membership', 'user'),
            'action_path' => '/user/set_membership',
            'template_path' => '/user/set_membership.html',
            'img_src' => '/shared/images/actions/membership.gif'
        ),
        'change_password' => array(
            'action_path' => '/user/change_password_action',
            'template_path' => '/user/change_password.html',
            'action_name' => strings :: get('change_password', 'user'),
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/actions/password_manage.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete_user','user'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/actions/delete.gif'
        ),
    );
  }
}

?>