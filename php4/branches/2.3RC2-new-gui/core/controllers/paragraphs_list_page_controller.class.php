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

class paragraphs_list_page_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/paragraphs_list_page/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/paragraphs_list_page/admin_display.html'
        ),
        'set_metadata' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'icon' => 'configure'
        ),
        'create_paragraph' => array(
            'template_path' => '/paragraph/create.html',
            'action_path' => '/paragraph/create_paragraph_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.generic',
            'action_name' => strings :: get('create_paragraph', 'paragraph'),
            'can_have_access_template' => true,
        ),
        'create_paragraphs_list_page' => array(
            'template_path' => '/paragraphs_list_page/create.html',
            'action_path' => '/paragraphs_list_page/create_paragraphs_list_page_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.folder',
            'action_name' => strings :: get('create_paragraphs_list_page', 'paragraph'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_paragraphs_list_page', 'paragraph'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/full_edit.html',
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
            'action_name' => strings :: get('delete_paragraphs_list_page', 'paragraph'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'icon' => 'delete'
        ),
    );
  }
}

?>