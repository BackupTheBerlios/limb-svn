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

class links_manager_page_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/links_manager/admin_display.html',
        ),
        'create_group' => array(
            'template_path' => '/links_manager/create_group.html',
            'action_path' => '/links_manager/create_links_group_action',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_group', 'links'),
        ),
        'delete_group' => array(
            'template_path' => '/links_manager/delete_group.html',
            'action_path' => '/links_manager/delete_links_group_action',
            'popup' => true,
        ),
        'edit_group' => array(
            'template_path' => '/links_manager/edit_group.html',
            'action_path' => '/links_manager/edit_links_group_action',
            'popup' => true,
        ),
        'create_link' => array(
            'template_path' => '/links_manager/create_link.html',
            'action_path' => '/links_manager/create_link_action',
            'popup' => true,
        ),
        'delete_link' => array(
            'template_path' => '/links_manager/delete_link.html',
            'action_path' => '/links_manager/delete_link_action',
            'popup' => true,
        ),
        'set_groups_priority' => array(
            'action_path' => '/links_manager/set_groups_priority_action',
            'popup' => true,
        ),
        'set_links_priority' => array(
            'action_path' => '/links_manager/set_links_priority_action',
            'popup' => true,
        ),
    );
  }
}

?>