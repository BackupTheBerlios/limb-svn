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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

class login_behaviour extends site_object_controller
{
  public function get_default_action()
  {
    return 'login';
  }

  public function get_login_action_properties()
  {
    return array();
  }

  public function define_login($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        'login.html')
                                  array(LIMB :: STATUS_OK => 'form'));

    $state_machine->registerState('form',
                                  array(LIMB_SIMPLE_PERMISSIONS_DIR . '/commands/login_form_command',
                                        'login_form'),
                                  array(LIMB :: STATUS_FORM_SUBMITTED => 'process',
                                        LIMB :: STATUS_FORM_DISPLAYED => 'render'));

    $state_machine->registerState('process',
                                  LIMB_SIMPLE_PERMISSIONS_DIR . '/commands/login_command',
                                  array(LIMB :: STATUS_ERROR => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  public function get_logout_action_properties()
  {
    return array();
  }

  public function define_logout($state_machine)
  {
    $state_machine->registerState('process', LIMB_SIMPLE_PERMISSIONS_DIR . '/commands/logout_command');
  }
}

?>