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
	
class photogallery_folder_controller extends site_object_controller
{
	function _define_actions()
	{
		return array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/photogallery_folder/display.html'
				),
				'admin_display' => array(
						'permissions_required' => 'rw',
						'template_path' => '/photogallery_folder/admin_display.html'
				),
				'set_metadata' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('set_metadata'),
						'action_path' => '/site_object/set_metadata_action',
						'template_path' => '/site_object/set_metadata.html',
						'img_src' => '/shared/images/configure.gif'
				),
				'create_photo' => array(
						'permissions_required' => 'w',
						'template_path' => '/photogallery_object/create.html',
						'action_path' => '/photogallery_object/create_photo_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_photo', 'photogallery'),
						'can_have_access_template' => true,
				),
				'create_photogallery_folder' => array(
						'permissions_required' => 'w',
						'template_path' => '/photogallery_folder/create.html',
						'action_path' => '/photogallery_folder/create_photogallery_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_photogallery_folder', 'photogallery'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'template_path' => '/photogallery_folder/edit.html',
						'action_path' => '/photogallery_folder/edit_photogallery_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/edit.gif',
						'action_name' => strings :: get('edit_photogallery_folder', 'photogallery'),
				),
				'publish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('publish'),
						'action_path' => '/doc_flow_object/set_publish_status_action',
						'img_src' => '/shared/images/publish.gif',
						'can_have_access_template' => true,
				),
				'unpublish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('unpublish'),
						'action_path' => '/doc_flow_object/set_publish_status_action',
						'img_src' => '/shared/images/unpublish.gif',
						'can_have_access_template' => true,
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_photogallery_folder', 'photogallery'),
						'action_path' => 'form_delete_site_object_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),

		);
	}
}

?>