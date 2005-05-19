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

class pricelist_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/pricelist_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/pricelist_folder/admin_display.html'
        ),
        'set_metadata' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'img_src' => '/shared/images/configure.gif'
        ),
        'create_pricelist_object' => array(
            'template_path' => '/pricelist_object/create.html',
            'action_path' => '/pricelist_object/create_pricelist_object_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_pricelist_object', 'pricelist'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'template_path' => '/site_object/edit.html',
            'action_path' => '/site_object/edit_action',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/edit.gif',
            'action_name' => strings :: get('edit_pricelist_folder', 'pricelist'),
        ),
        'publish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('publish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/publish.gif',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/unpublish.gif',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete_pricelist_folder', 'pricelist'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>