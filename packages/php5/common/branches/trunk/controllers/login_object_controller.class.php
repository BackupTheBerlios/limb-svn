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
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller.class.php');
	
class login_object_controller extends site_object_controller
{
  protected function _define_default_action()
  {
		return 'login';
	}
	
	protected function _define_actions()
	{
		return array(
				'login' => array(
						'permissions_required' => 'r',
						'action_path' => 'login_action',
						'template_path' => 'login.html'
				),
				'logout' => array(
						'permissions_required' => 'r',
						'action_path' => 'logout_action',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/site_object/edit_action',
						'template_path' => '/site_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'change_user_locale' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'action_name' => strings :: get('change_locale', 'user'),
						'action_path' => '/user/change_user_locale_action',
				),
		);
	}
}

?>