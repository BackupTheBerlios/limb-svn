<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
	
class user_generate_password_controller extends site_object_controller
{
	
	function user_generate_password_controller()
	{
		$this->_default_action = 'generate_password';

		$this->_actions = array(
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
 		

		parent :: site_object_controller();
	}
}

?>