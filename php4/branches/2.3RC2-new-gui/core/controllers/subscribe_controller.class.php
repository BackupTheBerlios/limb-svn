<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: subscribe_controller.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class subscribe_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'admin_display';
  }

  function _define_actions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/subscribe/admin_display.html'
        ),
        'create_subscribe_theme' => array(
            'template_path' => '/subscribe_theme/create.html',
            'action_path' => '/subscribe_theme/create_subscribe_theme_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'new.folder',
            'action_name' => strings :: get('create_subscribe_theme', 'subscribe'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'icon' => 'edit'
        ),
    );
  }
}

?>