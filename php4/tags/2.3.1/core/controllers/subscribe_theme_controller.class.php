<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: subscribe_theme_controller.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class subscribe_theme_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/subscribe_theme/admin_display.html'
        ),
        'admin_detail' => array(
            'template_path' => '/admin/object_detail_info.html',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/details.gif',
            'action_name' => strings :: get('detail_info'),
        ),
        'create_subscribe_mail' => array(
            'template_path' => '/subscribe_mail/create.html',
            'action_path' => '/subscribe_mail/create_subscribe_mail_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_subscribe_mail', 'subscribe'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_path' => '/subscribe_theme/edit_subscribe_theme_action',
            'template_path' => '/subscribe_theme/edit.html',
            'img_src' => '/shared/images/edit.gif',
            'action_name' => strings :: get('edit_subscribe_theme', 'subscribe'),
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
            'action_name' => strings :: get('delete_subscribe_theme', 'subscribe'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>