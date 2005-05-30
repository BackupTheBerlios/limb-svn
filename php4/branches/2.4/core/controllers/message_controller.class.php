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

class message_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/message/admin_display.html'
        ),
        'create_message' => array(
            'action_name' => strings :: get('create_message','message'),
            'action_path' => '/message/create_message_action',
            'template_path' => '/message/create.html',
            'icon' => 'new.generic',
            'popup' => true,
            'can_have_access_template' => true,
            'JIP' => true,
            'admin_main' => true,
            'menu' => true,
        ),
        'edit' => array(
            'action_name' => strings :: get('edit_message','message'),
            'action_path' => '/message/edit_message_action',
            'template_path' => '/message/edit.html',
            'icon' => 'edit',
            'popup' => true,
            'JIP' => true,
            'admin_main' => true,
        ),
        'delete' => array(
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'icon' => 'delete',
            'JIP' => true,
            'admin_secondary' => true,
         ),
    );
  }
}

?>