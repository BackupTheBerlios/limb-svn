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
	
class class_folder_controller extends site_object_controller
{
	function class_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/class_folder/display.html',
				),
				'set_group_access' => array(
						'permissions_required' => 'w',
						'template_path' => '/class_folder/set_group_access.html',
						'action_path' => '/class_folder/set_group_access',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/access_manage.gif',
						'action_name' => strings :: get('set_group_access'),
				),
				'set_group_access_template' => array(
						'permissions_required' => 'w',
						'template_path' => '/class_folder/set_group_access_template.html',
						'action_path' => '/class_folder/set_group_access_template_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/access_template_manage.gif',
						'action_name' => strings :: get('set_group_access_template'),
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