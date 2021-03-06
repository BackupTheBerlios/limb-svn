<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class files_folder_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/files_folder/admin_display.html'
        ),
        'create_file' => array(
            'template_path' => '/file/create.html',
            'action_name' => strings :: get('create_new_file', 'file'),
            'action_path' => '/files/create_file_action',
            'icon' => 'new.generic',
            'JIP' => true,
            'popup' => true,
            'can_have_access_template' => true,
        ),
        'create_files_folder' => array(
            'template_path' => '/files_folder/create.html',
            'action_path' => '/files_folder/create_files_folder_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.folder',
            'action_name' => strings :: get('create_files_folder', 'file'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_files_folder', 'file'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/full_edit.html',
            'icon' => 'edit'
        ),
        'delete' => array(
            'template_path' => '/site_object/delete.html',
            'action_path' => 'form_delete_site_object_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'delete',
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
