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

class faq_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/faq_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/faq_folder/admin_display.html'
        ),
        'admin_detail' => array(
            'template_path' => '/admin/object_detail_info.html',
            'popup' => true,
            'JIP' => true,
            'icon' => 'details',
            'action_name' => strings :: get('detail_info'),
        ),
        'create_faq_object' => array(
            'template_path' => '/faq_object/create.html',
            'action_path' => '/faq_object/create_faq_object_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.generic',
            'action_name' => strings :: get('create_faq_question','faq'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_faq_folder','faq'),
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
            'template_path' => '/news_object/display.html',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'icon' => 'unpublish',
            'template_path' => '/news_object/display.html',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete_faq_folder','faq'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'icon' => 'delete'
        ),
    );
  }
}

?>