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
require_once(LIMB_DIR . '/class/controllers/SiteObjectController.class.php');

class ObjectsAccessController extends SiteObjectController
{
  function _defineDefaultAction()
  {
    return 'admin_display';
  }

  function _defineActions()
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
            'img_src' => '/shared/images/access_manage.gif',
            'action_name' => Strings :: get('set_group_access'),
        ),
        'toggle' => array(
            'template_path' => '/objects_access/set_group_access.html',
            'action_path' => '/objects_access/group_objects_access_tree_toggle_action',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
    );
  }
}

?>