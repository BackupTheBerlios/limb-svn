<?php

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class images_folder_controller extends site_object_controller
{
	function images_folder_controller()
	{
		$this->_actions = array(
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
						'action_path' => '/images_folder/delete_images_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/rem.gif',
						'action_name' => strings :: get('delete'),
				),
				'image_select' => array(
						'permissions_required' => 'r',
						'template_path' => '/images_folder/image_select.html',
						'trasaction' => false,
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>