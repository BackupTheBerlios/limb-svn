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

class StateMachineCommand
{
  var $factory;
  var $states = array();
  var $state_history = array();
  var $initial_state = null;

  function StateMachineCommand(&$factory)
  {
    $this->factory =& $factory;
  }

  function registerState($state_name, $transitions_matrix = array())
  {
    if (isset($this->states[$state_name]))
      return throw_error(new LimbException('state has already been registered',
                              array('state_name' => $state_name)));

    $this->states[$state_name]['transitions'] = $transitions_matrix;

    return true;
  }

  function setInitialState($state)
  {
    $this->initial_state = $state;
  }

  function perform()
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
      $result = $this->_performStateCommand($state);

      if (isset($this->states[$state]['transitions'][$result]))
        $next_state = $this->states[$state]['transitions'][$result];
      elseif(isset($this->states[$state]['transitions'][STATE_MACHINE_BY_DEFAULT]))
        $next_state = $this->states[$state]['transitions'][STATE_MACHINE_BY_DEFAULT];
      else
        return $result;

      if(catch_error('LimbException', $e))
         return throw_error($e);
    }
  }

  function _performStateCommand($state)
  {
    if (!isset($this->states[$state]))
    {
      return throw_error(new LimbException('illegal state',
                            array('state_name' => $state)));
    }

    $method = 'perform' .  $state;
    $result = $this->factory->$method();

    $this->state_history[] = array($state => $result);

    return $result;
  }

  function getEndState()
  {
    return end($this->state_history);
  }

  function getStateHistory()
  {
    return $this->state_history;
  }

  function _getNextState($state, $result)
  {
  }

  function reset()
  {
    $this->states = array();
  }
}

?>