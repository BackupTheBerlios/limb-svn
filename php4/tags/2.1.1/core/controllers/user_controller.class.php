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
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class user_controller extends site_object_controller
{
	function _define_actions()
	{
		return array(
				'display' => array(
						'template_path' => '/user/display.html',
						'permissions_required' => 'r',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('edit_user', 'user'),
						'action_path' => '/user/edit_user_action',
						'template_path' => '/user/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'set_membership' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('membership', 'user'),
						'action_path' => '/user/set_membership',
						'template_path' => '/user/set_membership.html',
						'img_src' => '/shared/images/membership.gif'
				),
				'change_password' => array(
						'permissions_required' => 'w',
						'action_path' => '/user/change_password_action',
						'template_path' => '/user/change_password.html',
						'action_name' => strings :: get('change_password', 'user'),
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/password_manage.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_user','user'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
	}
}

?>