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

class UserChangeOwnPasswordController extends SiteObjectController
{
  protected function _defineDefaultAction()
  {
    return 'change_own_password';
  }

  protected function _defineActions()
  {
    return array(
      'change_own_password' => array(
          'action_path' => '/user/change_own_password_action',
          'template_path' => '/user/change_own_password.html',
          'action_name' => Strings :: get('change_own_password', 'user'),
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