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
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/i18n/Strings.class.php');

class SiteObjectController
{
  var $behaviour;

  function SiteObjectController(&$behaviour)
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
    $this->_startTransaction();

    $this->_performAction($request);

    if(catch('LimbException', $e))
    {
      $this->_rollbackTransaction();
      return throw($e);
    }

    $this->_commitTransaction();
  }

  function &_getStateMachine()
  {
    include_once(LIMB_DIR . '/class/commands/StateMachine.class.php');
    return new StateMachine();
  }

  function _performAction(&$request)
  {
    if(!$action = $this->getRequestedAction($request))
      return throw(new LimbException('action not defined in state machine',
                              array('action' => $action,
                                    'class' => get_class($this->behaviour))));

    $state_machine =& $this->_getStateMachine();

    call_user_func(array($this->behaviour, 'define' . $action), $state_machine);

    $res = $state_machine->run();

    Debug :: addTimingPoint('action performed');

    return $res;
  }

  function _startTransaction()
  {
    startUserTransaction();
  }

  function _commitTransaction()
  {
    commitUserTransaction();
  }

  function _rollbackTransaction()
  {
    rollbackUserTransaction();
  }
}

?>