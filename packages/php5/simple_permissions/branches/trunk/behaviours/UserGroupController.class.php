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

class UserGroupController extends SiteObjectController
{
  protected function _defineDefaultAction()
  {
    return 'admin_display';
  }

  protected function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/user_group/admin_display.html',
        ),
        'edit' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('edit_user_group', 'user_group'),
            'action_path' => '/user_group/edit_user_group_action',
            'template_path' => '/user_group/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('delete_user_group', 'user_group'),
            'action_path' => '/form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>