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
  protected $behaviour;

  function __construct($behaviour)
  {
    $this->behaviour = $behaviour;
  }

  public function getBehaviour()
  {
    return $this->behaviour;
  }

  public function getRequestedAction($request)
  {
    if (!$action = $request->get('action'))
      $action = $this->behaviour->getDefaultAction();

    if (!$this->behaviour->actionExists($action))
      return null;

    return $action;
  }

  public function process($request)
  {
    $this->_startTransaction();

    try
    {
      $this->_performAction($request);
      $this->_commitTransaction();
    }
    catch(LimbException $e)
    {
      $this->_rollbackTransaction();
      throw $e;
    }
  }

  protected function _getStateMachine()
  {
    include_once(LIMB_DIR . '/class/commands/StateMachine.class.php');
    return new StateMachine();
  }

  protected function _performAction($request)
  {
    if(!$action = $this->getRequestedAction($request))
      throw new LimbException('action not defined in state machine',
                              array('action' => $action,
                                    'class' => get_class($this->behaviour)));

    $state_machine = $this->_getStateMachine();

    call_user_func(array($this->behaviour, 'define_' . $action), $state_machine);

    $state_machine->run();

    Debug :: addTimingPoint('action performed');
  }

  protected function _startTransaction()
  {
    startUserTransaction();
  }

  protected function _commitTransaction()
  {
    commitUserTransaction();
  }

  protected function _rollbackTransaction()
  {
    rollbackUserTransaction();
  }
}

?>