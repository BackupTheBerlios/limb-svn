<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller.class.php');
	
class class_folder_controller extends site_object_controller
{
  protected function _define_default_action()
  {
		return 'admin_display';
	} 
	
	protected function _define_actions()
	{
		return array(
				'admin_display' => array(
						'template_path' => '/class_folder/admin_display.html',
				),
				'set_group_access' => array(
						'template_path' => '/class_folder/set_group_access.html',
						'action_path' => '/class_folder/set_group_access',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/access_manage.gif',
						'action_name' => strings :: get('set_group_access'),
				),
				'set_group_access_template' => array(
						'template_path' => '/class_folder/set_group_access_template.html',
						'action_path' => '/class_folder/set_group_access_template_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/access_template_manage.gif',
						'action_name' => strings :: get('set_group_access_template'),
				),
				'edit' => array(
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