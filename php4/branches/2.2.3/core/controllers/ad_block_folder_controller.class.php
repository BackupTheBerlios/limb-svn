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
require_once(LIMB_DIR . 'core/lib/i18n/strings.class.php');
	
class ad_block_folder_controller extends site_object_controller
{
	function _define_actions()
	{
		return array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/ad_block_folder/display.html',
						'popup' => true
				),
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/ad_block_folder/admin_display.html',
						'action_name' => strings :: get('admin_display'),
				),
				'create_ad_block' => array(
						'permissions_required' => 'r',
						'template_path' => '/ad_block_object/create.html',
						'action_path' => '/ad_block_object/create_ad_block_object_action',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('create_ad_block', 'ad'),
						'img_src' => '/shared/images/new.generic.gif',
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
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				), 
		);
	}
}

?>