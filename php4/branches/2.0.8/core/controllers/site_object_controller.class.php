<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action_factory.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');
require_once(LIMB_DIR . 'core/template/template.class.php');
require_once(LIMB_DIR . 'core/template/empty_template.class.php');
require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'core/model/response/response.class.php');
	
class site_object_controller
{
	var $_actions = array();
	
	var $_current_action = '';

	var $_default_action = 'display';
	
	var $_view = null;
	
	var $_response = null;

	function site_object_controller()
	{
	}

	function create($class_name)
	{	
		include_class($class_name, '/core/controllers/');
  	return create_object($class_name);
	}
		
	function determine_action()
	{	
		if (isset($_REQUEST['action']))
			$action = $_REQUEST['action'];
		else
			$action = $this->_default_action;
		
		if (!$this->action_exists($action))
		{
			debug :: write_warning(
				'action not found', 
				__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array(
					'class' => get_class($this),
					'action' => $action,
					'default_action' => $this->_default_action
				));
			return false;
		}
		
		$this->_current_action = $action;
		
		return $this->_current_action;
	}
	
	function get_default_action()
	{
		return $this->_default_action;
	}
	
	function get_actions_definitions()
	{
		return $this->_actions;
	}
	
	function action_exists($action)
	{
		$actions = $this->get_actions_definitions();
		return isset($actions[$action]);
	}
	
	function get_permissions_required()
	{	
		return $this->get_current_action_property('permissions_required');
	}
	
	function set_action($action)
	{
		$this->_current_action = $action;
	}
	
	function get_action()
	{
		return $this->_current_action;
	}
	
	function get_action_name($action)
	{
		if(!$name = $this->get_action_property($action, 'action_name'))
			$name = $action;
		
		return $name;
	}
	
	function process()
	{
		if(!$this->_current_action)
		{
			debug :: write_error('current action not defined', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
			return new response();
		}
			
		$this->_start_transaction();
		
		$this->_perform_action();
				
		$this->_end_transaction();
		
		return $this->_response;
	}
	
	function _perform_action()
	{
		$action =& $this->get_action_object();
		
		if($view =& $this->get_view())
			$action->set_view($view);

		$this->_response = $action->perform();
		
		debug :: add_timing_point('action performed');
		
		if($this->_response->is_problem())
			debug :: write_error('action failed', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	}
	
	function display_view()
	{
		$view =& $this->get_view();
		
		$view->display();
		
		debug :: add_timing_point('template executed');
	}
	
	function _start_transaction()
	{
		if($this->is_transaction_required())	
			start_user_transaction();
	}
	
	function _end_transaction()
	{
		if(!$this->is_transaction_required())
			return;

		if($this->_response->is_success())
			commit_user_transaction();
		else
			rollback_user_transaction();
	}
				
	function is_transaction_required()
	{
		$requires_transaction = $this->get_current_action_property('transaction');
		
		if ($requires_transaction === false)
			return false;
		else
			return true;
	}
	
	function & get_action_object()
	{
		if (!$action_path = $this->get_current_action_property('action_path'))
			$action_path = 'empty_action';
		
		return $this->_create_action($action_path);
	}
	
	function &_create_action($action_path)
	{
		$action =& action_factory :: create($action_path);
		return $action;
	}
	
	function & get_view()
	{
		if($this->_view)
			return $this->_view;
				
		$this->_view =& $this->_create_template();

	  debug :: add_timing_point('template created');
		
		return $this->_view;
	}
	
	function &_create_template()
	{
		if($template_path = $this->get_current_action_property('template_path'))
			return new template($template_path);
		else
			return new empty_template();			
	}
	
	function get_current_action_property($property_name)
	{
		if (!$this->_current_action)
			return null;		
			
		return $this->get_action_property($this->_current_action, $property_name);
	}
	
	function get_action_property($action, $property_name)
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