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

class LoginBehaviour extends SiteObjectController
{
  function getDefaultAction()
  {
    return 'login';
  }

  function getLoginActionProperties()
  {
    return array();
  }

  function defineLogin(&$state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/commands/use_view_command',
                                        'login.html')
                                  array(LIMB_STATUS_OK => 'form'));

    $state_machine->registerState('form',
                                  array(LIMB_SIMPLE_PERMISSIONS_DIR . '/commands/login_form_command',
                                        'login_form'),
                                  array(LIMB_STATUS_FORM_SUBMITTED => 'process',
                                        LIMB_STATUS_FORM_DISPLAYED => 'render'));

    $state_machine->registerState('process',
                                  LIMB_SIMPLE_PERMISSIONS_DIR . '/commands/login_command',
                                  array(LIMB_STATUS_ERROR => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/commands/display_view_command');
  }

  function getLogoutActionProperties()
  {
    return array();
  }

  function defineLogout(&$state_machine)
  {
    $state_machine->registerState('process', LIMB_SIMPLE_PERMISSIONS_DIR . '/commands/logout_command');
  }
}

?>