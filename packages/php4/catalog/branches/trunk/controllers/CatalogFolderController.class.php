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
require_once(LIMB_DIR . '/core/controllers/SiteObjectController.class.php');

class CatalogFolderController extends SiteObjectController
{
  function _defineActions()
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
            'action_name' => Strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'img_src' => '/shared/images/configure.gif'
        ),
        'create_catalog_folder' => array(
            'template_path' => '/catalog_folder/create.html',
            'action_path' => '/catalog_folder/create_catalog_folder_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.folder.gif',
            'action_name' => Strings :: get('create_catalog_folder', 'catalog'),
            'can_have_access_template' => true,
        ),
        'create_catalog_object' => array(
            'template_path' => '/catalog_object/create.html',
            'action_path' => '/catalog_object/create_catalog_object_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => Strings :: get('create_catalog_object', 'catalog'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'template_path' => '/catalog_folder/edit.html',
            'action_path' => '/catalog_folder/edit_catalog_folder_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/edit.gif',
            'action_name' => Strings :: get('edit_catalog_folder', 'catalog'),
        ),
        'publish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('publish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/publish.gif',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/unpublish.gif',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('delete_catalog_folder', 'catalog'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>