<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/i18n/strings.class.php');
	
abstract class site_object_controller
{	
  var $behaviour;
  
	function __construct($behaviour)
	{
	  $this->behaviour = $behaviour;
	}
		
	public function get_requested_action($request)
	{		    	  
		if (!$action = $request->get('action'))
			$action = $this->behaviour->get_default_action();
		
		if (!$this->behaviour->action_exists($action))
      return null;
		
		return $action;
	}
		
	public function process($request)
	{			
		$this->_start_transaction();
		
		try
		{
		  $this->_perform_action($request);
		  $this->_commit_transaction();
		}
		catch(LimbException $e)
		{
		  $this->_rollback_transaction();
		  throw $e;
		}
	}
  
  protected function _get_state_machine()
  {
    include_once(LIMB_DIR . '/class/commands/state_machine.class.php');
    return new state_machine();
  }
	
	protected function _perform_action($request)
	{
    if(!$action = $this->get_requested_action($request))
      throw new LimbException('action not defined in state machine', 
                              array('action' => $action, 
                                    'class' => get_class($this->behaviour)));
      
    $state_machine = $this->_get_state_machine();
    
    call_user_func(array($this->behaviour, 'define_' . $action), $state_machine);
    
    $state_machine->run();
    
		debug :: add_timing_point('action performed');
	}
	
	protected function _start_transaction()
	{
    start_user_transaction();
	}
	
	protected function _commit_transaction()
	{
		commit_user_transaction();
	}
	
  protected function _rollback_transaction()
  {			
    rollback_user_transaction();
  }		
}

?>