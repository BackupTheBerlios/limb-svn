<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: informer_folder_controller.class.php 109 2004-04-01 15:25:23Z mike $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class informer_folder_controller extends site_object_controller
{
	function informer_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/informer_folder/display.html'
				),
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/informer_folder/admin_display.html'
				),
				'create_informer' => array(
						'permissions_required' => 'w',
						'template_path' => '/informer_object/create.html',
						'action_path' => '/informer_object/create_informer_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_informer', 'informer'),
						'can_have_access_template' => true,
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
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete'),
						'action_path' => '/site_object/delete_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),

		);
 		
		parent :: site_object_controller();
	}
}

?>