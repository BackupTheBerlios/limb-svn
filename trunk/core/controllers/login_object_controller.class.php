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
	
class login_object_controller extends site_object_controller
{
	function login_object_controller()
	{
		$this->_actions = array(
				'display' => array(
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

		);
		
		parent :: site_object_controller();
	}
}

?>