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

class useful_links_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'permissions_required' => 'r',
            'template_path' => '/useful_links_folder/display.html'
        ),
        'admin_display' => array(
            'permissions_required' => 'r',
            'template_path' => '/useful_links_folder/admin_display.html'
        ),
        'set_metadata' => array(
            'permissions_required' => 'w',
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'img_src' => '/shared/images/configure.gif'
        ),
        'create_link' => array(
            'permissions_required' => 'w',
            'template_path' => '/useful_link/create.html',
            'action_path' => '/useful_link/create_useful_link_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_link', 'useful_link'),
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'permissions_required' => 'w',
            'template_path' => '/site_object/delete.html',
            'action_path' => 'form_delete_site_object_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/rem.gif',
            'action_name' => strings :: get('delete'),
        ),
    );
  }
}

?>