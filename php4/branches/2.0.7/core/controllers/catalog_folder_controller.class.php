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
	
class catalog_folder_controller extends site_object_controller
{
	function catalog_folder_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/catalog_folder/display.html'
				),
				'admin_display' => array(
						'permissions_required' => 'rw',
						'template_path' => '/catalog_folder/admin_display.html'
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
				'create_catalog_folder' => array(
						'permissions_required' => 'w',
						'template_path' => '/catalog_folder/create.html',
						'action_path' => '/catalog_folder/create_catalog_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_catalog_folder', 'catalog'),
						'can_have_access_template' => true,
				),
				'create_catalog_object' => array(
						'permissions_required' => 'w',
						'template_path' => '/catalog_object/create.html',
						'action_path' => '/catalog_object/create_catalog_object_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_catalog_object', 'catalog'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'template_path' => '/catalog_folder/edit.html',
						'action_path' => '/catalog_folder/edit_catalog_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/edit.gif',
						'action_name' => strings :: get('edit_catalog_folder', 'catalog'),
				),
				'publish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('publish'),
						'action_path' => '/doc_flow_object/publish_action',
						'img_src' => '/shared/images/publish.gif',
						'can_have_access_template' => true,
				),
				'unpublish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('unpublish'),
						'action_path' => '/doc_flow_object/unpublish_action',
						'img_src' => '/shared/images/unpublish.gif',
						'can_have_access_template' => true,
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_catalog_folder', 'catalog'),
						'action_path' => '/catalog_folder/delete_catalog_folder_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
				'order' => array(
						'permissions_required' => 'r',
						'action_path' => 'tree_change_order_action', 
						'display_in_breadcrumbs' => false,
						'popup' => true,
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>