<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: photogallery_folder_controller.class.php 33 2004-03-10 16:05:12Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class paragraphs_list_page_controller extends site_object_controller
{
	function paragraphs_list_page_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/paragraphs_list_page/display.html'
				),
				'admin_display' => array(
						'permissions_required' => 'rw',
						'template_path' => '/paragraphs_list_page/admin_display.html'
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
				'create_paragraph' => array(
						'permissions_required' => 'w',
						'template_path' => '/paragraph/create.html',
						'action_path' => '/paragraph/create_paragraph_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_paragraph', 'paragraph'),
						'can_have_access_template' => true,
				),
				'create_paragraphs_list_page' => array(
						'permissions_required' => 'w',
						'template_path' => '/paragraphs_list_page/create.html',
						'action_path' => '/paragraphs_list_page/create_paragraphs_list_page_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_paragraphs_list_page', 'paragraph'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'template_path' => '/paragraphs_list_page/edit.html',
						'action_path' => '/paragraphs_list_page/edit_paragraphs_list_page_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/edit.gif',
						'action_name' => strings :: get('edit_paragraphs_list_page', 'paragraph'),
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
						'action_name' => strings :: get('delete_paragraphs_list_page', 'paragraph'),
						'action_path' => '/paragraphs_list_page/delete_paragraphs_list_page_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),

		);
 		
		parent :: site_object_controller();
	}
}

?>