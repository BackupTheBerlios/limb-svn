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

define('STATE_MACHINE_BY_DEFAULT', 255);

class StateMachine
{
  var $states = array();
  var $initial_state = null;

  function registerState($state_name, &$command, $transitions_matrix = array())
  {
    if (isset($this->states[$state_name]))
      return throw(new LimbException('state has already been registered',
                              array('state_name' => $state_name)));

    $this->states[$state_name]['command'] =& $command;
    $this->states[$state_name]['transitions'] = $transitions_matrix;

    return true;
  }

  function setInitialState($state)
  {
    $this->initial_state = $state;
  }

  function run()
  {
    if (!count($this->states))
      return;

    if($this->initial_state)
      $next_state = $this->initial_state;
    else
    {
      reset($this->states);
      $next_state = key($this->states);
    }

    while($next_state != false)
    {
      $state = $next_state;
      $next_state = $this->_processState($state);

      if(catch('Exception', $e))
         return throw($e);
    }
  }

  function _processState($state_name)
  {
    if (!isset($this->states[$state_name]))
    {
      return throw(new LimbException('illegal state',
                            array('state_name' => $state_name)));
    }

    $state_data =& $this->states[$state_name];
    resolveHandle($command =& $state_data['command']);
    $result = $command->perform();

    if (isset($state_data['transitions'][$result]))
      return $state_data['transitions'][$result];
    elseif(isset($state_data['transitions'][STATE_MACHINE_BY_DEFAULT]))
      return $state_data['transitions'][STATE_MACHINE_BY_DEFAULT];
    else
      return false;
  }

  function reset()
  {
    $this->states = array();
  }
}

?>