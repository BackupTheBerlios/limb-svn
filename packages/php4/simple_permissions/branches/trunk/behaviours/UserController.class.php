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
require_once(LIMB_DIR . '/core/controllers/SiteObjectController.class.php');

class UserController extends SiteObjectController
{
  function _defineDefaultAction()
  {
    return 'admin_display';
  }

  function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/user/admin_display.html',
        ),
        'edit' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('edit_user', 'user'),
            'action_path' => '/user/edit_user_action',
            'template_path' => '/user/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'set_membership' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('membership', 'user'),
            'action_path' => '/user/set_membership',
            'template_path' => '/user/set_membership.html',
            'img_src' => '/shared/images/membership.gif'
        ),
        'change_password' => array(
            'action_path' => '/user/change_password_action',
            'template_path' => '/user/change_password.html',
            'action_name' => Strings :: get('change_password', 'user'),
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/password_manage.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('delete_user','user'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
        'change_user_locale' => array(
            'popup' => true,
            'action_name' => Strings :: get('change_locale', 'user'),
            'action_path' => '/user/change_user_locale_action',
        ),
    );
  }
}

?>