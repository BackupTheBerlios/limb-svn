<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ServiceController.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');
require_once(LIMB_DIR . '/core/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/i18n/Strings.class.php');

class ServiceController
{
  var $behaviour;

  function ServiceController(&$behaviour)
  {
    $this->behaviour =& $behaviour;
  }

  function & getBehaviour()
  {
    return $this->behaviour;
  }

  function getRequestedAction(&$request)
  {
    if (!$action = $request->get('action'))
      $action = $this->behaviour->getDefaultAction();

    if (!$this->behaviour->actionExists($action))
      return null;

    return $action;
  }

  function process(&$request)
  {
    if(!$action = $this->getRequestedAction($request))
      return throw(new LimbException('action not defined in state machine',
                              array('action' => $action,
                                    'class' => get_class($this->behaviour))));

    $state_machine =& $this->_getStateMachine();

    call_user_func(array($this->behaviour, 'define' . $action), &$state_machine);

    $state_machine->run();

    Debug :: addTimingPoint('action performed');
  }

  function &_getStateMachine()
  {
    include_once(LIMB_DIR . '/core/commands/StateMachine.class.php');
    return new StateMachine();
  }
}

?>