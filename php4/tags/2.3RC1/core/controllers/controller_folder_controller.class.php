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

class controller_folder_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/controller_folder/admin_display.html',
        ),
        'admin_display' => array(
            'template_path' => '/controller_folder/admin_display.html',
        ),
        'set_group_access' => array(
            'template_path' => '/controller_folder/set_group_access.html',
            'action_path' => '/controller_folder/set_group_access',
            'popup' => true,
            'img_src' => '/shared/images/access_manage.gif',
            'action_name' => strings :: get('set_group_access'),
        ),
        'set_group_access_template' => array(
            'template_path' => '/controller_folder/set_group_access_template.html',
            'action_path' => '/controller_folder/set_group_access_template_action',
            'popup' => true,
            'img_src' => '/shared/images/access_template_manage.gif',
            'action_name' => strings :: get('set_group_access_template'),
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
    );
  }
}

?>