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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
	
class files_folder_controller extends site_object_controller
{
  protected function _define_default_action()
  {
		return 'admin_display';
	} 
	
	protected function _define_actions()
	{
		return array(
				'admin_display' => array(
						'template_path' => '/files_folder/admin_display.html'
				),
				'create_file' => array(
						'template_path' => '/file/create.html',
						'action_name' => strings :: get('create_new_file', 'file'),
						'action_path' => '/files/create_file_action',
						'img_src' => '/shared/images/new.generic.gif',
						'JIP' => true,
						'popup' => true,
						'can_have_access_template' => true,
				),
				'create_files_folder' => array(
						'template_path' => '/files_folder/create.html',
						'action_path' => '/files_folder/create_files_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.folder.gif',
						'action_name' => strings :: get('create_files_folder', 'file'),
						'can_have_access_template' => true,
				),
				'edit_files_folder' => array(
						'template_path' => '/files_folder/edit.html',
						'action_path' => '/files_folder/edit_files_folder_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/edit.gif',
						'action_name' => strings :: get('edit_files_folder', 'file'),
				),
				'delete' => array(
						'template_path' => '/site_object/delete.html',
						'action_path' => 'form_delete_site_object_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/rem.gif',
						'action_name' => strings :: get('delete'),
				),
				'file_select' => array(
						'template_path' => '/files_folder/file_select.html',
						'trasaction' => false,
				),
		);
	}
}

?>