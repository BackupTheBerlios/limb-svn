<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class poll_answer_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'permissions_required' => 'r',
            'template_path' => '/poll_answer/admin_display.html',
        ),
        'edit' => array(
            'permissions_required' => 'w',
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_poll_answer','poll'),
            'action_path' => '/poll_answer/edit_poll_answer_action',
            'template_path' => '/poll_answer/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'publish' => array(
            'permissions_required' => 'w',
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('publish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/publish.gif',
            'template_path' => '/news_object/display.html',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'permissions_required' => 'w',
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/unpublish.gif',
            'template_path' => '/news_object/display.html',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'permissions_required' => 'w',
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete_poll_answer','poll'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>