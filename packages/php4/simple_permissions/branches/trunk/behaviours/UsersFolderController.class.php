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
require_once(LIMB_DIR . '/core/controllers/SiteObjectController.class.php');

class UsersFolderController extends SiteObjectController
{
  function _defineDefaultAction()
  {
    return 'admin_display';
  }

  function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/users_folder/admin_display.html'
        ),
        'create_user' => array(
            'template_path' => '/user/create.html',
            'action_path' => '/user/create_user_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => Strings :: get('create_user', 'user'),
            'can_have_access_template' => true,
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