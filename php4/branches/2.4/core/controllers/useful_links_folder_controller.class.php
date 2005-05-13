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

class useful_links_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/useful_links_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/useful_links_folder/admin_display.html'
        ),
        'set_metadata' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'icon' => 'configure'
        ),
        'create_link' => array(
            'template_path' => '/useful_link/create.html',
            'action_path' => '/useful_link/create_useful_link_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.generic',
            'action_name' => strings :: get('create_link', 'useful_link'),
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'template_path' => '/site_object/delete.html',
            'action_path' => 'form_delete_site_object_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'delete',
            'action_name' => strings :: get('delete'),
        ),
    );
  }
}

?>