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

class StateMachine
{
  const BY_DEFAULT = 255;

  protected $states = array();
  protected $initial_state = null;

  public function registerState($state_name, $command, $transitions_matrix = array())
  {
    if (isset($this->states[$state_name]))
      throw new LimbException('state already been registered',
                              array('state_name' => $state_name));

    $this->states[$state_name]['command'] = $command;
    $this->states[$state_name]['transitions'] = $transitions_matrix;
  }

  public function setInitialState($state)
  {
    $this->initial_state = $state;
  }

  public function run()
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
    }
  }

  protected function _processState($state_name)
  {
    if (!isset($this->states[$state_name]))
    {
      throw new LimbException('illegal state',
                            array('state_name' => $state_name));
    }

    $state_data = $this->states[$state_name];
    resolveHandle($state_data['command']);
    $result = $state_data['command']->perform();

    if (isset($state_data['transitions'][$result]))
      return $state_data['transitions'][$result];
    elseif(isset($state_data['transitions'][self :: BY_DEFAULT]))
      return $state_data['transitions'][self :: BY_DEFAULT];
    else
      return false;
  }

  public function reset()
  {
    $this->states = array();
  }
}

?>