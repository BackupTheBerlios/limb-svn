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
	
class user_group_controller extends site_object_controller
{
  protected function _define_default_action()
  {
		return 'admin_display';
	} 
	
	protected function _define_actions()
	{
		return array(
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/user_group/admin_display.html',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('edit_user_group', 'user_group'),
						'action_path' => '/user_group/edit_user_group_action',
						'template_path' => '/user_group/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_user_group', 'user_group'),
						'action_path' => '/form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
	}
}

?>