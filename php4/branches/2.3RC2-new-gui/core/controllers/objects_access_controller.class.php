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

class objects_access_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/objects_access/set_group_access.html',
            'action_path' => '/objects_access/set_group_objects_access',
        ),
        'set_group_access' => array(
            'template_path' => '/objects_access/set_group_access.html',
            'action_path' => '/objects_access/set_group_objects_access',
            'JIP' => true,
            'img_src' => '/shared/images/actions/access_manage.gif',
            'action_name' => strings :: get('set_group_access'),
        ),
        'toggle' => array(
            'template_path' => '/objects_access/set_group_access.html',
            'action_path' => '/objects_access/group_objects_access_tree_toggle_action',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/actions/edit.gif'
        ),

    );
  }
}

?>