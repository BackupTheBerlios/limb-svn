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

require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . '/core/lib/util/complex_array.class.php');

define('ACCESSOR_TYPE_GROUP', 0);
define('ACCESSOR_TYPE_USER', 1);

class access_policy
{
	var $_action_access = array();

  var $_cached_controller_accessible_actions = array();
  
	function access_policy()
	{
	}

	function & instance()
	{	
		$obj =&	instantiate_object('access_policy');
		return $obj;
	}
	
	function get_accessible_objects($object_ids, $permissions = 'r')
	{
		if (!count($object_ids))
			return array();
			
		$in_ids = implode(',', $object_ids);

    $accessor_ids = implode(',', $this->get_accessor_ids());
    
    if (!$accessor_ids)
      return array();
			
		$db =& db_factory :: instance();
			
		$sql = "SELECT soa.object_id as id
			FROM sys_site_object as sso, sys_object_access as soa
			WHERE soa.object_id IN ({$in_ids})
			AND sso.id = soa.object_id ";
			
		$sql	.= " AND soa.accessor_id IN ({$accessor_ids})";
		
		if ($permissions == 'r')
			$sql .=" AND soa.r = 1";
		elseif ($permissions == 'w')
			$sql .=" AND soa.w = 1";
		elseif ($permissions == 'rw')
			$sql .=" AND soa.w = 1 AND soa.r = 1";
		else
			error('permissions are not allowed',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
    		 array('permissions' => $permissions));
	
  	$db->sql_exec($sql);
  	$temp =& $db->get_array('id');
  	
  	$result = array();
  	foreach($object_ids as $id)
  	{
  		if (isset($temp[$id]))
  			$result[$id] =& $temp[$id];
  	}
  	  	
  	return array_keys($result);
	}

	function & get_user_object_access()
	{
		$result =& $this->_get_object_access(ACCESSOR_TYPE_USER);
		
		return $result;
	}

	function & get_group_object_access()
	{
		$result =& $this->_get_object_access(ACCESSOR_TYPE_GROUP);
		
		return $result;
	}

	function & _get_object_access($accessor_type)
	{
		$db_table =& db_table_factory :: instance('sys_object_access');
		
		$arr =& $db_table->get_list('accessor_type=' . $accessor_type);
	
		$result =& $this->_process_object_access_rows($arr);
		
		return $result;
	}
	
	function & get_user_object_access_by_ids($ids)
	{
		$result =& $this->_get_object_access_by_ids($ids, ACCESSOR_TYPE_USER);
		
		return $result;
	}

	function & get_group_object_access_by_ids($ids)
	{
		$result =& $this->_get_object_access_by_ids($ids, ACCESSOR_TYPE_GROUP);
		
		return $result;
	}
	
	function & _get_object_access_by_ids(&$ids, $accessor_type)
	{
		if (!is_array($ids) || !count($ids))
			return array();
			
		$db_table =& db_table_factory :: instance('sys_object_access');
		
		$ids_sql = 'object_id IN ('. implode(',', $ids) . ') AND accessor_type=' . $accessor_type;
		
		$arr =& $db_table->get_list($ids_sql);
	
		$result =& $this->_process_object_access_rows($arr);
		
		return $result;
	}

	function & _process_object_access_rows($rows)
	{
		$result = array();
		foreach($rows as $id => $data)
		{
			$result[$data['object_id']][$data['accessor_id']]['r'] = (int)$data['r'];
			$result[$data['object_id']][$data['accessor_id']]['w'] = (int)$data['w'];
		}
			
		return $result;
	}
	

	function & _get_action_access_by_controller($controller_id, $accessor_type)
	{
		$db_table =& db_table_factory :: instance('sys_action_access');
		
		$condition = 'controller_id ='. $controller_id . ' AND accessor_type=' . $accessor_type;
		
		$arr =& $db_table->get_list($condition);
	
		$result =& $this->_process_action_access_rows($arr);
		
		return $result;
	}

	function & get_user_action_access_by_controller($controller_id)
	{
		$result =& $this->_get_action_access_by_controller($controller_id, ACCESSOR_TYPE_USER);
		return $result;
	}
	
	function & get_group_action_access_by_controller($controller_id)
	{
		$result =& $this->_get_action_access_by_controller($controller_id, ACCESSOR_TYPE_GROUP);
		return $result;
	}

	function & _process_action_access_rows($rows)
	{
		$result = array();
		foreach($rows as $id => $data)
			$result[$data['accessor_id']][$data['action_name']] = 1;
			
		return $result;
	}
		
	function assign_actions_to_objects(&$objects)
	{
		$controllers = array();
		$permitted_actions = array();
		
		if(isset($objects['id']))
		{
			$available_permissions = $this->_load_objects_available_permissions(array($objects['id']));
			$arr[] =& $objects;
		}
		else
		{
			$available_permissions = $this->_load_objects_available_permissions(complex_array :: get_column_values('id', $objects));
			$arr =& $objects;
		}
		
		foreach($arr as $key => $data)
		{
			$controller_id = $data['controller_id'];
			
			$arr[$key]['actions'] = array();
						
			if (!isset($actions_definitions[$controller_id]))
			{
				$site_object_controller =& $this->_get_controller($data['controller_name']);
				$actions_definitions[$controller_id] = $site_object_controller->get_actions_definitions();
			}	

			if (!isset($permitted_type_actions[$controller_id]))
				$permitted_actions[$controller_id] = $this->_get_controller_accessible_actions($controller_id);
			
			$permitted_type_actions =& $permitted_actions[$controller_id];			
			$all_actions =  $actions_definitions[$controller_id];

			if (!isset($available_permissions[$data['id']]))
				continue;
				
			$object_available_permissions = $available_permissions[$data['id']];

			foreach($all_actions as $action_name => $action_params)
			{
				if (!isset($action_params['permissions_required']))
				{
			   	error('action permissions not set',
		    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
		    		array(
		    			'site_object_controller' => $controller_controller_name,
		    			'action' => $action_name,
	    			)
		    	);
				}
				
				if ((($action_params['permissions_required'] == 'r' && $object_available_permissions['r'] > 0)
				 || ($action_params['permissions_required'] == 'w' && $object_available_permissions['w'] > 0)
				 || ($action_params['permissions_required'] == 'rw' && ($object_available_permissions['w'] > 0 && $object_available_permissions['r'] > 0)))
				&& (isset($permitted_type_actions[$action_name])))
				{
					$arr[$key]['actions'][$action_name] = $action_params;
				}	
			}			
		}
	}
		
	//for mocking
	function &_get_controller($controller_name)
	{
	  $controller =& site_object_controller :: create($controller_name);
		return $controller;
	}
	
	function & _load_objects_available_permissions($objects_ids)
	{
		$db =& db_factory :: instance();
		
		$in_ids = implode(',', $objects_ids);

    $accessor_ids = implode(',', $this->get_accessor_ids());
		
		$sql = "SELECT soa.object_id as id, SUM(soa.r) as r, SUM(soa.w) as w
			FROM sys_object_access as soa
			WHERE soa.object_id IN ({$in_ids})
			AND soa.accessor_id IN ({$accessor_ids})
			GROUP BY soa.object_id";
	
  	$db->sql_exec($sql);
  	$result =& $db->get_array('id');
  	
  	return $result;
	}
	
	function _get_controller_accessible_actions($controller_id)
	{
	  if(isset($this->_cached_controller_accessible_actions[$controller_id]))
	    return $this->_cached_controller_accessible_actions[$controller_id];
	    
		$accessor_ids = $this->get_accessor_ids();
		
		$in_ids = implode(',', $accessor_ids);

		$db =& db_factory :: instance();

		$sql = "SELECT saa.action_name FROM sys_action_access as saa
			WHERE saa.controller_id = {$controller_id} AND
			saa.accessor_id IN ({$in_ids})
			GROUP BY saa.action_name";
		
    $db->sql_exec($sql);
    $result = $db->get_array('action_name');
		
		$this->_cached_controller_accessible_actions[$controller_id] = $result;
		return $result;	
	}
	
	function get_accessor_ids()
	{
		$accessor_ids = array();
		
		$user =& user :: instance();
		
		if(($user_id = $user->get_id()) != DEFAULT_USER_ID)
			$accessor_ids[] = $user_id;
			
		foreach(array_keys($user->get_groups()) as $group_id)	
			$accessor_ids[] = $group_id;
		
		return $accessor_ids;	
	}
	
	function save_user_action_access($controller_id, $policy_array)
	{
		return $this->_save_action_access($controller_id, $policy_array, ACCESSOR_TYPE_USER);
	}

	function save_group_action_access($controller_id, $policy_array)
	{
		return $this->_save_action_access($controller_id, $policy_array, ACCESSOR_TYPE_GROUP);
	}
	
	function _save_action_access($controller_id, $policy_array, $accessor_type)
	{
		$db_table	=& db_table_factory :: instance('sys_action_access');
		$conditions['controller_id'] = $controller_id;
		$conditions['accessor_type'] = $accessor_type;

		$db_table->delete($conditions);

		foreach($policy_array as $accessor_id => $access_data)
		{
			foreach($access_data as $action_name => $is_accessible)
			{
				if (!$is_accessible)
					continue;

				$data = array();
				$data['accessor_id'] = $accessor_id;
				$data['controller_id'] = $controller_id;
				$data['action_name'] = $action_name;
				$data['accessor_type'] = $accessor_type;

				$db_table->insert($data);
			}
		}				

		return true;
	}
	
	function save_object_access(&$object, &$parent_object, $action = '')
	{
		if(empty($action))
		{
			$parent_controller =& $parent_object->get_controller();
			$action = $parent_controller->determine_action();
		}

		$controller_id = $parent_object->get_controller_id();
		$object_id = $object->get_id();
		$parent_object_id = $parent_object->get_id();

		$group_template = $this->get_group_action_access_template($controller_id, $action);
		$user_template = $this->get_user_action_access_template($controller_id, $action);
		
		if (!count($group_template))
			$group_result = $this->copy_group_object_access($object_id, $parent_object_id);
		else	
			$group_result = $this->save_group_object_access(array($object_id => $group_template));

		if (!count($user_template))
			$user_result = $this->copy_user_object_access($object_id, $parent_object_id);
		else	
			$user_result = $this->save_user_object_access(array($object_id => $user_template));
		
		if (!$group_result && !$user_result)	
		{
			 debug ::	write_error('parent object has no acccess records at all',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('parent_id' => $parent_object_id)
    	);
			return false;
		}	
		else
			return true;	
	}
	
	function save_object_access_for_action(&$object, $action)
	{
		$controller_id = $object->get_controller_id();
		$object_id = $object->get_id();
		
		$user_template = $this->get_user_action_access_template($controller_id, $action);
		$group_template = $this->get_group_action_access_template($controller_id, $action);

		if(!$user_template && !$group_template)
		{
			 debug ::	write_error('Access template is not set',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('action' => $action, 'controller_name' => get_class($object))
    	);
			return false;
		}

		$db_table	=& db_table_factory :: instance('sys_object_access');

		$conditions['object_id'] = $object_id;

		$db_table->delete($conditions);

		$this->save_group_object_access(array($object_id => $group_template));
		$this->save_user_object_access(array($object_id => $user_template));
		
		return true;	
	}

	function save_user_object_access($policy_array, $accessor_ids = array())
	{
		return $this->_save_object_access($policy_array, ACCESSOR_TYPE_USER, $accessor_ids);
	}

	function save_group_object_access($policy_array, $accessor_ids = array())
	{
		return $this->_save_object_access($policy_array, ACCESSOR_TYPE_GROUP, $accessor_ids);
	}

	function _save_object_access($policy_array, $accessor_type, $accessor_ids = array())
	{
		$db_table	=& db_table_factory :: instance('sys_object_access');
		
		foreach($policy_array as $object_id => $access_data)
		{
		  $conditions = 'object_id='. 1*$object_id . ' AND accessor_type=' . $accessor_type;
		  if (count($accessor_ids))
		    $conditions .= ' AND '. sql_in('accessor_id', $accessor_ids);
      
			$db_table->delete($conditions);

			foreach($access_data as $accessor_id => $rights)
			{
				$data = array();
				
				if (isset($rights['r']) && $rights['r'])
					$data['r']	= 1;
				else	
					$data['r']	= 0;

				if (isset($rights['w']) &&  $rights['w'])
					$data['w']	= 1;
				else	
					$data['w']	= 0;
				
				if (!$data['r'] && !$data['w'])
					continue;

				$data['accessor_id'] = $accessor_id;
				$data['object_id'] = $object_id;
				$data['accessor_type'] = $accessor_type;

				$db_table->insert($data);
			}
		}	
					
		return true;
	}

	function copy_user_object_access($object_id, $source_id)
	{
		return $this->_copy_object_access($object_id, $source_id, ACCESSOR_TYPE_USER);
	}
	
	function copy_group_object_access($object_id, $source_id)
	{
		return $this->_copy_object_access($object_id, $source_id, ACCESSOR_TYPE_GROUP);
	}
	
	function _copy_object_access($object_id, $source_id, $accessor_type)
	{
		$db_table	=& db_table_factory :: instance('sys_object_access');

		$conditions['object_id'] = $object_id;
		$conditions['accessor_type'] = $accessor_type;
	
		$db_table->delete($conditions);

		$conditions['object_id'] = $source_id;
		
		$rows = $db_table->get_list($conditions);
		if(!count($rows))
			return false;

		foreach($rows as $id => $data)
		{
			unset($data['id']);
			$data['object_id'] = $object_id;
			$db_table->insert($data);
		}

		return true;
	}

	function save_user_action_access_template($controller_id, $template_array)
	{
		$db_table	=& db_table_factory :: instance('sys_user_object_access_template');
		$item_db_table	=& db_table_factory :: instance('sys_user_object_access_template_item');
		$db_table->delete('controller_id='. $controller_id);		

		foreach($template_array as $action_name => $access_data)
		{
			$data = array();
			$data['controller_id'] = $controller_id;
			$data['action_name'] = $action_name;
			$db_table->insert($data);
			$template_id = $db_table->get_last_insert_id();
			
			foreach($access_data as $user_id => $rights)
			{
				$data = array();
				$data['user_id'] = $user_id;
				$data['template_id'] = $template_id;

				if (isset($rights['r']) && $rights['r'])
					$data['r']	= 1;
				else	
					$data['r']	= 0;

				if (isset($rights['w']) &&  $rights['w'])
					$data['w']	= 1;
				else	
					$data['w']	= 0;
				
				if (!$data['r'] && !$data['w'])
					continue;
					
				$item_db_table->insert($data);
			}
		}				
		return true;
	}


	function save_group_action_access_template($controller_id, $template_array)
	{
		$db_table	=& db_table_factory :: instance('sys_group_object_access_template');
		$item_db_table	=& db_table_factory :: instance('sys_group_object_access_template_item');
		$db_table->delete('controller_id='. $controller_id);		

		foreach($template_array as $action_name => $access_data)
		{
			$data = array();
			$data['controller_id'] = $controller_id;
			$data['action_name'] = $action_name;
			$db_table->insert($data);
			$template_id = $db_table->get_last_insert_id();
			
			foreach($access_data as $group_id => $rights)
			{
				$data = array();
				$data['group_id'] = $group_id;
				$data['template_id'] = $template_id;

				if (isset($rights['r']) && $rights['r'])
					$data['r']	= 1;
				else	
					$data['r']	= 0;

				if (isset($rights['w']) &&  $rights['w'])
					$data['w']	= 1;
				else	
					$data['w']	= 0;
				
				if (!$data['r'] && !$data['w'])
					continue;
					
				$item_db_table->insert($data);
			}
		}				
		return true;
	}
	
	function get_user_action_access_templates($controller_id)
	{
		$db =& db_factory :: instance();
		
		$sql = "SELECT * FROM sys_user_object_access_template as stoat, 
			sys_user_object_access_template_item as stoati
			WHERE stoat.controller_id = {$controller_id} AND
			stoati.template_id = stoat.id";
		
    $db->sql_exec($sql);
    $all_template_records = $db->get_array();
		
		if (!count($all_template_records))
			return array();
		
		$result = array();	
		foreach($all_template_records as $data)
		{
			$result[$data['action_name']][$data['user_id']]['r'] = $data['r'];
			$result[$data['action_name']][$data['user_id']]['w'] = $data['w'];
		}

		return $result;
	}

	function get_group_action_access_templates($controller_id)
	{
		$db =& db_factory :: instance();
		
		$sql = "SELECT * FROM sys_group_object_access_template as stoat, 
			sys_group_object_access_template_item as stoati
			WHERE stoat.controller_id = {$controller_id} AND
			stoati.template_id = stoat.id";
		
    $db->sql_exec($sql);
    $all_template_records = $db->get_array();
		
		if (!count($all_template_records))
			return array();
		
		$result = array();	
		foreach($all_template_records as $data)
		{
			$result[$data['action_name']][$data['group_id']]['r'] = $data['r'];
			$result[$data['action_name']][$data['group_id']]['w'] = $data['w'];
		}

		return $result;
	}

	function get_user_action_access_template($controller_id, $action_name)
	{
		$template = $this->get_user_action_access_templates($controller_id);
		
		if (isset($template[$action_name]))
			return $template[$action_name];
		else
			return array();	
	}

	function get_group_action_access_template($controller_id, $action_name)
	{
		$template = $this->get_group_action_access_templates($controller_id);
		
		if (isset($template[$action_name]))
			return $template[$action_name];
		else
			return array();	
	}
}
?>