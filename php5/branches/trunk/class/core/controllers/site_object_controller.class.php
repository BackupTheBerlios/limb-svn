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
require_once(LIMB_DIR . 'class/lib/db/db_table.class.php');
require_once(LIMB_DIR . 'class/template/template.class.php');
require_once(LIMB_DIR . 'class/template/empty_template.class.php');
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'class/i18n/strings.class.php');
	
abstract class site_object_controller
{
	protected $_actions = array();
	
	protected $_current_action = '';

	protected $_default_action = '';
	
	protected $_request = null;
	
	function __construct()
	{
	  $this->_actions = $this->_define_actions();
	  
	  $this->_default_action = $this->_define_default_action();
	}
	
	protected function _define_actions()
	{
	  return array();
	}
	
	protected function _define_default_action()
	{
	  return 'display';
	}
	
	public function get_action($request)
	{		    
	  if($this->_request === $request && $this->_current_action)
	    return $this->_current_action;
	    
	  $this->_request = $request;
	  
		if (!$action = $request->get('action'))
			$action = $this->_default_action;
		
		if (!$this->action_exists($action))
		{
		  throw new LimbException('action not found', 
          				array(
          					'class' => get_class($this),
          					'action' => $action,
          					'default_action' => $this->_default_action
          				)
          			);
		}
		
		$this->_current_action = $action;
		
		return $this->_current_action;
	}
	
	public function get_default_action()
	{
		return $this->_default_action;
	}
	
	public function get_actions_definitions()
	{
		return $this->_actions;
	}
	
	public function action_exists($action)
	{
		$actions = $this->get_actions_definitions();
		return isset($actions[$action]);
	}
	
	public function get_action_name($action)
	{
		if(!$name = $this->get_action_property($action, 'action_name'))
			$name = $action;
		
		return $name;
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
    $action = $this->get_action($request);
    
    if(!method_exists($this, '_define_' . $action))
      throw new LimbException('action not defined in state machine', 
                              array('action' => $action, 
                                    'class' => get_class($this)));
      
    $state_machine = $this->_get_state_machine();
    
    call_user_func(array($this, '_define_' . $action), $state_machine);
    
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
		
	public function get_action_property($action, $property_name)
	{
		$actions = $this->get_actions_definitions();
			
		if (!isset($actions[$action]))
			return null;
			
		$action_definition = $actions[$action];
		if (!isset($action_definition[$property_name]))	
			return null;
		else
			return $action_definition[$property_name];
	}
}

?>