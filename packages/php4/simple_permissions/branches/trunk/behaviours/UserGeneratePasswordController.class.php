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

class UserGeneratePasswordController extends SiteObjectController
{
  function _defineDefaultAction()
  {
    return 'generate_password';
  }

  function _defineActions()
  {
    return array(
        'generate_password' => array(
          'action_path' => '/user/generate_password_action',
          'template_path' => '/user/generate_password.html',
          'action_name' => Strings :: get('generate_password', 'user'),
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