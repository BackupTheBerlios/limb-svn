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

class catalog_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/catalog_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/catalog_folder/admin_display.html'
        ),
        'set_metadata' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'icon' => 'configure'
        ),
        'create_catalog_folder' => array(
            'template_path' => '/catalog_folder/create.html',
            'action_path' => '/catalog_folder/create_catalog_folder_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.folder',
            'action_name' => strings :: get('create_catalog_folder', 'catalog'),
            'can_have_access_template' => true,
        ),
        'create_catalog_object' => array(
            'template_path' => '/catalog_object/create.html',
            'action_path' => '/catalog_object/create_catalog_object_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.generic',
            'action_name' => strings :: get('create_catalog_object', 'catalog'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_catalog_folder', 'catalog'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'icon' => 'edit'
        ),
        'publish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('publish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'icon' => 'publish',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'icon' => 'unpublish',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete_catalog_folder', 'catalog'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'icon' => 'delete'
        ),
    );
  }
}

?>