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
require_once(LIMB_DIR . 'class/core/actions/action_factory.class.php');
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
	
	protected $_view = null;
	
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
	
	public function get_action($request = null)
	{	
	  if($request === null)
	    $request = request :: instance();
	    
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
	
	public function get_permissions_required($request = null)
	{	
		return $this->get_current_action_property('permissions_required', $request);
	}
			
	public function get_action_name($action)
	{
		if(!$name = $this->get_action_property($action, 'action_name'))
			$name = $action;
		
		return $name;
	}
	
	public function process($request, $response)
	{			
		$this->_start_transaction($request);
		
		try
		{
		  $this->_perform_action($request, $response);
		  $this->_commit_transaction($request);
		}
		catch(LimbException $e)
		{
		  $this->_roll_back_transaction($request);
		  throw $e;
		}
	}
	
	protected function _perform_action($request, $response)
	{
		$action = $this->get_action_object($request);
		
		if($view = $this->get_view($request))
			$action->set_view($view);

		$action->perform($request, $response);
		
		debug :: add_timing_point('action performed');
	}
	
	public function display_view($request = null)
	{
		$view = $this->get_view($request);
		
		$view->display();
		
		debug :: add_timing_point('template executed');
	}
	
	protected function _start_transaction($request = null)
	{
		if($this->is_transaction_required($request))	
			start_user_transaction();
	}
	
	protected function _commit_transaction($request = null)
	{
		if(!$this->is_transaction_required($request))
			return;
			
		commit_user_transaction();
	}
	
  protected function _rollback_transaction($request = null)
  {
		if($this->is_transaction_required($request))	
      rollback_user_transaction();
  }
  				
	public function is_transaction_required($request = null)
	{
		$requires_transaction = $this->get_current_action_property('transaction', $request);
		
		if ($requires_transaction === false)
			return false;
		else
			return true;
	}
	
	public function get_action_object($request = null)
	{
	  if(!$action_path = $this->get_current_action_property('action_path', $request))
	    $action_path = 'empty_action';
		
		return $this->_create_action($action_path);
	}
	
	protected function _create_action($action_path)
	{
		return action_factory :: create($action_path);
	}
	
	public function get_view($request = null)
	{
		if($this->_view)
			return $this->_view;
				
		$this->_view = $this->_create_template($request);

	  debug :: add_timing_point('template created');
		
		return $this->_view;
	}
	
	protected function _create_template($request = null)
	{
		if($template_path = $this->get_current_action_property('template_path', $request))
			return new template($template_path);
		else
			return new empty_template();
	}
	
	public function get_current_action_property($property_name, $request = null)
	{	
	  try
	  {		
		  return $this->get_action_property($this->get_action($request), $property_name);
		}
		catch(LimbException $e)
		{
		  return null;
		}
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