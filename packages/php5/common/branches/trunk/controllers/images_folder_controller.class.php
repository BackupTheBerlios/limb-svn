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
	
class images_folder_controller extends site_object_controller
{
	protected function _define_actions()
	{
		return array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/images_folder/display.html'
				),
				'create_image' => array(
						'permissions_required' => 'w',
						'template_path' => '/image/create.html',
						'action_path' => '/images/create_image_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_image', 'image'),
						'can_have_access_template' => true,
				),
				'create_images_folder' => array(
						'permissions_required' => 'w',
						'template_path' => '/images_folder/create.html',
						'action_path' => '/images_folder/create_images_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_images_folder', 'image'),
						'can_have_access_template' => true,
				),
				'edit_images_folder' => array(
						'permissions_required' => 'w',
						'template_path' => '/images_folder/edit.html',
						'action_path' => '/images_folder/edit_images_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/edit.gif',
						'action_name' => strings :: get('edit_images_folder', 'image'),
				),
				'delete' => array(
						'permissions_required' => 'w',
						'template_path' => '/site_object/delete.html',
						'action_path' => 'form_delete_site_object_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/rem.gif',
						'action_name' => strings :: get('delete'),
				),
				'image_select' => array(
						'permissions_required' => 'r',
						'action_name' => strings :: get('select_image', 'image'),
						'action_path' => '/images_folder/image_select_action',
						'template_path' => '/images_folder/image_select.html',
						'trasaction' => false,
				),
		);
	}
}

?>