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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

class faq_object_controller extends site_object_controller
{
  protected function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/faq_object/display.html',
        ),
        'admin_detail' => array(
            'template_path' => '/admin/object_detail_info.html',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/details.gif',
            'action_name' => strings :: get('detail_info'),
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_faq_question', 'faq'),
            'action_path' => '/faq_object/edit_faq_object_action',
            'template_path' => '/faq_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'publish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('publish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/publish.gif',
            'template_path' => '/news_object/display.html',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/unpublish.gif',
            'template_path' => '/news_object/display.html',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete_faq_question','faq'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>