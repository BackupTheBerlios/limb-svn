<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/controllers/SiteObjectController.class.php');

class FilesFolderController extends SiteObjectController
{
  protected function _defineDefaultAction()
  {
    return 'admin_display';
  }

  protected function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/files_folder/admin_display.html'
        ),
        'create_file' => array(
            'template_path' => '/file/create.html',
            'action_name' => Strings :: get('create_new_file', 'file'),
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
            'action_name' => Strings :: get('create_files_folder', 'file'),
            'can_have_access_template' => true,
        ),
        'edit_files_folder' => array(
            'template_path' => '/files_folder/edit.html',
            'action_path' => '/files_folder/edit_files_folder_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/edit.gif',
            'action_name' => Strings :: get('edit_files_folder', 'file'),
        ),
        'delete' => array(
            'template_path' => '/site_object/delete.html',
            'action_path' => 'form_delete_site_object_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/rem.gif',
            'action_name' => Strings :: get('delete'),
        ),
        'file_select' => array(
            'template_path' => '/files_folder/file_select.html',
            'trasaction' => false,
        ),
    );
  }
}

?>