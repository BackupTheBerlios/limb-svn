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

class ImagesFolderController extends SiteObjectController
{
  protected function _defineDefaultAction()
  {
    return 'admin_display';
  }


  protected function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/images_folder/admin_display.html'
        ),
        'create_image' => array(
            'template_path' => '/image/create.html',
            'action_path' => '/images/create_image_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => Strings :: get('create_image', 'image'),
            'can_have_access_template' => true,
        ),
        'create_images_folder' => array(
            'template_path' => '/images_folder/create.html',
            'action_path' => '/images_folder/create_images_folder_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.folder.gif',
            'action_name' => Strings :: get('create_images_folder', 'image'),
            'can_have_access_template' => true,
        ),
        'edit_images_folder' => array(
            'template_path' => '/images_folder/edit.html',
            'action_path' => '/images_folder/edit_images_folder_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/edit.gif',
            'action_name' => Strings :: get('edit_images_folder', 'image'),
        ),
        'delete' => array(
            'template_path' => '/site_object/delete.html',
            'action_path' => 'form_delete_site_object_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/rem.gif',
            'action_name' => Strings :: get('delete'),
        ),
        'image_select' => array(
            'action_name' => Strings :: get('select_image', 'image'),
            'action_path' => '/images_folder/image_select_action',
            'template_path' => '/images_folder/image_select.html',
            'trasaction' => false,
        ),
    );
  }
}

?>