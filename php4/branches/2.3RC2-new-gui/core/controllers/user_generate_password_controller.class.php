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
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class user_generate_password_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'generate_password';
  }

  function _define_actions()
  {
    return array(
        'generate_password' => array(
          'action_path' => '/user/generate_password_action',
          'template_path' => '/user/generate_password.html',
          'action_name' => strings :: get('generate_password', 'user'),
        ),
        'password_generated' => array(
          'template_path' => '/user/password_generated.html',
        ),
        'password_not_generated' => array(
          'template_path' => '/user/password_not_generated.html',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/actions/edit.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/actions/delete.gif'
        ),
    );
  }
}
?>