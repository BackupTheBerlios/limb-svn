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
require_once(LIMB_DIR . '/class/core/controllers/SiteObjectController.class.php');

class UserGroupsFolderController extends SiteObjectController
{
  protected function _defineDefaultAction()
  {
    return 'admin_display';
  }

  protected function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/user_groups_folder/admin_display.html'
        ),
        'create_user_group' => array(
            'template_path' => '/user_group/create.html',
            'action_path' => '/user_group/create_user_group_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.folder.gif',
            'action_name' => Strings :: get('create_user_group', 'user_group'),

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