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
	
class user_group_controller extends site_object_controller
{
	function user_group_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/user_group/display.html',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('edit_user_group', 'user_group'),
						'action_path' => '/user/edit_user_group_action',
						'template_path' => '/user_group/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_user_group', 'user_group'),
						'action_path' => '/user/delete_user_group_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		

		parent :: site_object_controller();
	}
}

?>