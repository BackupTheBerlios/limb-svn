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
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');

class chat_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'permissions_required' => 'r',
            'template_path' => '/chat/display.html',
        ),
        'admin_display' => array(
            'permissions_required' => 'r',
            'template_path' => '/chat/admin_display.html',
            'action_name' => strings :: get('admin_display'),
        ),
        'create_chat_room' => array(
            'permissions_required' => 'w',
            'template_path' => '/chat_room/create.html',
            'action_path' => '/chat_room/create_chat_room_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_chat_room', 'chat'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'permissions_required' => 'w',
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'user_settings' => array(
            'permissions_required' => 'r',
            'action_path' => '/chat_room/user_settings_action',
            'template_path' => '/chat/user_settings_form.html',
            'action_name' => strings :: get('update_user_settings', 'chat'),
        ),
    );
  }
}

?>