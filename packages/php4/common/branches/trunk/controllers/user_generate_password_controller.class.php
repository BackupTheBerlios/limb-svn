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
	
class user_generate_password_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'generate_password';
  }
  
	function _define_actions()
	{
		return array(
				'generate_password' => array(
					'permissions_required' => 'r',
					'action_path' => '/user/generate_password_action',
					'template_path' => '/user/generate_password.html',
					'action_name' => strings :: get('generate_password', 'user'),
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

		);
	}
}

?>