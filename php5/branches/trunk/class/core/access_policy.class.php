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
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . 'class/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'class/core/user.class.php');

class access_policy
{
  const ACCESSOR_TYPE_GROUP = 0;
  const ACCESSOR_TYPE_USER = 1;
  
  protected static $_instance = null;
  
	protected $_action_access = array();

  protected $_cached_type_accessible_actions = array();
  	
	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new access_policy();

    return self :: $_instance;	
	}	
	
	public function get_accessible_objects($object_ids, $permissions = 'r', $class_id = null)
	{
		if (!count($object_ids))
			return array();
			
		$in_ids = implode(',', $object_ids);

    $accessor_ids = implode(',', $this->get_accessor_ids());
    
    if (!$accessor_ids)
      return array();
			
		$db = db_factory :: instance();
			
		$sql = "SELECT soa.object_id as id
			FROM sys_site_object as sso, sys_object_access as soa
			WHERE soa.object_id IN ({$in_ids})
			AND sso.id = soa.object_id ";
			
		if ($class_id)
			$sql .=" AND sso.class_id = {$class_id}";

		$sql	.= " AND soa.accessor_id IN ({$accessor_ids})";
		
		if ($permissions == 'r')
			$sql .=" AND soa.r = 1";
		elseif ($permissions == 'w')
			$sql .=" AND soa.w = 1";
		elseif ($permissions == 'rw')
			$sql .=" AND soa.w = 1 AND soa.r = 1";
		else
			throw new LimbException('permissions are not allowed',
    		 array('permissions' => $permissions));
	
  	$db->sql_exec($sql);
  	$temp = $db->get_array('id');
  	
  	$result = array();
  	foreach($object_ids as $id)
  	{
  		if (isset($temp[$id]))
  			$result[$id] = $temp[$id];
  	}
  	  	
  	return array_keys($result);
	}

	public function get_user_object_access()
	{
		return $this->_get_object_access(self :: ACCESSOR_TYPE_USER);
	}

	public function get_group_object_access()
	{
		return $this->_get_object_access(self :: ACCESSOR_TYPE_GROUP);
	}

	protected function _get_object_access($accessor_type)
	{
		$db_table = db_table_factory :: create('sys_object_access');
		
		$arr = $db_table->get_list('accessor_type=' . $accessor_type);
	
		return $this->_process_object_access_rows($arr);
	}
	
	public function get_user_object_access_by_ids($ids)
	{
		return $this->_get_object_access_by_ids($ids, self :: ACCESSOR_TYPE_USER);
	}

	public function get_group_object_access_by_ids($ids)
	{
		return $this->_get_object_access_by_ids($ids, self :: ACCESSOR_TYPE_GROUP);
	}
	
	protected function _get_object_access_by_ids($ids, $accessor_type)
	{
		if (!is_array($ids) || !count($ids))
			return array();
			
		$db_table = db_table_factory :: create('sys_object_access');
		
		$ids_sql = 'object_id IN ('. implode(',', $ids) . ') AND accessor_type=' . $accessor_type;
		
		$arr = $db_table->get_list($ids_sql);
	
		return $this->_process_object_access_rows($arr);
	}

	protected function _process_object_access_rows($rows)
	{
		$result = array();
		foreach($rows as $id => $data)
		{
			$result[$data['object_id']][$data['accessor_id']]['r'] = (int)$data['r'];
			$result[$data['object_id']][$data['accessor_id']]['w'] = (int)$data['w'];
		}
			
		return $result;
	}

	protected function _get_action_access_by_class($class_id, $accessor_type)
	{
		$db_table = db_table_factory :: create('sys_action_access');
		
		$condition = 'class_id ='. $class_id . ' AND accessor_type=' . $accessor_type;
		
		$arr = $db_table->get_list($condition);
	
		return $this->_process_action_access_rows($arr);
	}

	public function get_user_action_access_by_class($class_id)
	{
		return $this->_get_action_access_by_class($class_id, self :: ACCESSOR_TYPE_USER);
	}
	
	public function get_group_action_access_by_class($class_id)
	{
		return $this->_get_action_access_by_class($class_id, self :: ACCESSOR_TYPE_GROUP);
	}

	protected function _process_action_access_rows($rows)
	{
		$result = array();
		foreach($rows as $id => $data)
			$result[$data['accessor_id']][$data['action_name']] = 1;
			
		return $result;
	}
		
	public function assign_actions_to_objects(&$objects)
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
			$class_id = $data['class_id'];
			
			$arr[$key]['actions'] = array();
						
			if (!isset($actions_definitions[$class_id]))
			{
				$site_object_controller = $this->_get_controller($data['class_name']);
				$actions_definitions[$class_id] = $site_object_controller->get_actions_definitions();
			}	

			if (!isset($permitted_type_actions[$class_id]))
				$permitted_actions[$class_id] = $this->_get_type_accessible_actions($class_id);
			
			$permitted_type_actions =& $permitted_actions[$class_id];			
			$all_actions =  $actions_definitions[$class_id];

			if (!isset($available_permissions[$data['id']]))
				continue;
				
			$object_available_permissions = $available_permissions[$data['id']];

			foreach($all_actions as $action_name => $action_params)
			{
				if (!isset($action_params['permissions_required']))
				{
			   	throw new LimbException('action permissions not set',
		    		array(
		    			'site_object_controller' => $controller_class_name,
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
	protected function _get_controller($class_name)
	{
		return site_object_factory :: create($class_name)->get_controller();
	}
	
	protected function _load_objects_available_permissions($objects_ids)
	{
		$db = db_factory :: instance();
		
		$in_ids = implode(',', $objects_ids);

    $accessor_ids = implode(',', $this->get_accessor_ids());
		
		$sql = "SELECT soa.object_id as id, SUM(soa.r) as r, SUM(soa.w) as w
			FROM sys_object_access as soa
			WHERE soa.object_id IN ({$in_ids})
			AND soa.accessor_id IN ({$accessor_ids})
			GROUP BY soa.object_id";
	
  	$db->sql_exec($sql);
  	return $db->get_array('id');
	}
	
	protected function _get_type_accessible_actions($class_id)
	{
	  if(isset($this->_cached_type_accessible_actions[$class_id]))
	    return $this->_cached_type_accessible_actions[$class_id];
	    
		$accessor_ids = $this->get_accessor_ids();
		
		$in_ids = implode(',', $accessor_ids);

		$db = db_factory :: instance();

		$sql = "SELECT saa.action_name as action_name FROM sys_action_access as saa
			WHERE saa.class_id = {$class_id} AND
			saa.accessor_id IN ({$in_ids})
			GROUP BY saa.action_name";
		
    $db->sql_exec($sql);
    $result = $db->get_array('action_name');
		
		$this->_cached_type_accessible_actions[$class_id] = $result;
		return $result;	
	}
	
	public function get_accessor_ids()
	{
		$accessor_ids = array();
		
		$user = user :: instance();
		
		if(($user_id = $user->get_id()) != user :: DEFAULT_USER_ID)
			$accessor_ids[] = $user_id;
			
		foreach(array_keys($user->get_groups()) as $group_id)	
			$accessor_ids[] = $group_id;
		
		return $accessor_ids;	
	}
	
	public function save_user_action_access($class_id, $policy_array)
	{
		return $this->_save_action_access($class_id, $policy_array, self :: ACCESSOR_TYPE_USER);
	}

	public function save_group_action_access($class_id, $policy_array)
	{
		return $this->_save_action_access($class_id, $policy_array, self :: ACCESSOR_TYPE_GROUP);
	}
	
	protected function _save_action_access($class_id, $policy_array, $accessor_type)
	{
		$db_table	= db_table_factory :: create('sys_action_access');
		$conditions['class_id'] = $class_id;
		$conditions['accessor_type'] = $accessor_type;

		$db_table->delete($conditions);

		foreach($policy_array as $accessor_id => $access_data)
		{
			foreach($access_data as $action_name => $is_accessible)
			{
				if (!$is_accessible)
					continue;

				$data = array();
      	$data['id'] = null;
				$data['accessor_id'] = $accessor_id;
				$data['class_id'] = $class_id;
				$data['action_name'] = $action_name;
				$data['accessor_type'] = $accessor_type;

				$db_table->insert($data);
			}
		}				

		return true;
	}
	
	public function save_object_access($object, $parent_object, $action = '')
	{
		if(empty($action))
		{
			$parent_controller = $parent_object->get_controller();
			try
			{
				$action = $parent_controller->get_action();
			}
			catch(LimbException $e)
			{
				$action = '';
			}
		}

		$class_id = $parent_object->get_class_id();
		$object_id = $object->get_id();
		$parent_object_id = $parent_object->get_id();

		$group_template = $this->get_group_action_access_template($class_id, $action);
		$user_template = $this->get_user_action_access_template($class_id, $action);
		
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
       throw new LimbException('parent object has no acccess records at all',
    		array('parent_id' => $parent_object_id)
    	);
		}	
		else
			return true;	
	}
	
	public function save_object_access_for_action($object, $action)
	{
		$class_id = $object->get_class_id();
		$object_id = $object->get_id();
		
		$user_template = $this->get_user_action_access_template($class_id, $action);
		$group_template = $this->get_group_action_access_template($class_id, $action);

		if(!$user_template && !$group_template)
		{
			 throw new LimbException('access template is not set',
    		array('action' => $action, 'class_name' => get_class($object))
    	);
		}

		$db_table	= db_table_factory :: create('sys_object_access');

		$conditions['object_id'] = $object_id;

		$db_table->delete($conditions);

		$this->save_group_object_access(array($object_id => $group_template));
		$this->save_user_object_access(array($object_id => $user_template));
		
		return true;	
	}

	public function save_user_object_access($policy_array)
	{
		return $this->_save_object_access($policy_array, self :: ACCESSOR_TYPE_USER);
	}

	public function save_group_object_access($policy_array)
	{
		return $this->_save_object_access($policy_array, self :: ACCESSOR_TYPE_GROUP);
	}

	protected function _save_object_access($policy_array, $accessor_type)
	{
		$db_table	= db_table_factory :: create('sys_object_access');

		foreach($policy_array as $object_id => $access_data)
		{
			$conditions['object_id'] = $object_id;
			$conditions['accessor_type'] = $accessor_type;

			foreach($access_data as $accessor_id => $rights)
			{
				$conditions['accessor_id'] = $accessor_id;
				$db_table->delete($conditions);
			
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

      	$data['id'] = null;
				$data['accessor_id'] = $accessor_id;
				$data['object_id'] = $object_id;
				$data['accessor_type'] = $accessor_type;

				$db_table->insert($data);
			}
		}	
					
		return true;
	}

	public function copy_user_object_access($object_id, $source_id)
	{
		return $this->_copy_object_access($object_id, $source_id, self :: ACCESSOR_TYPE_USER);
	}
	
	public function copy_group_object_access($object_id, $source_id)
	{
		return $this->_copy_object_access($object_id, $source_id, self :: ACCESSOR_TYPE_GROUP);
	}
	
	protected function _copy_object_access($object_id, $source_id, $accessor_type)
	{
		$db_table	= db_table_factory :: create('sys_object_access');

		$conditions['object_id'] = $object_id;
		$conditions['accessor_type'] = $accessor_type;
	
		$db_table->delete($conditions);

		$conditions['object_id'] = $source_id;
		
		$rows = $db_table->get_list($conditions);
		if(!count($rows))
			return false;

		foreach($rows as $id => $data)
		{
    	$data['id'] = null;
			$data['object_id'] = $object_id;
			$db_table->insert($data);
		}

		return true;
	}

	public function save_user_action_access_template($class_id, $template_array)
	{
		$db_table	= db_table_factory :: create('sys_user_object_access_template');
		$item_db_table	= db_table_factory :: create('sys_user_object_access_template_item');
		$db_table->delete('class_id='. $class_id);		

		foreach($template_array as $action_name => $access_data)
		{
			$data = array();
    	$data['id'] = null;
			$data['class_id'] = $class_id;
			$data['action_name'] = $action_name;
			$db_table->insert($data);
			$template_id = $db_table->get_last_insert_id();
			
			foreach($access_data as $user_id => $rights)
			{
				$data = array();
      	$data['id'] = null;
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

	public function save_group_action_access_template($class_id, $template_array)
	{
		$db_table	= db_table_factory :: create('sys_group_object_access_template');
		$item_db_table	= db_table_factory :: create('sys_group_object_access_template_item');
		$db_table->delete('class_id='. $class_id);		

		foreach($template_array as $action_name => $access_data)
		{
			$data = array();
    	$data['id'] = null;
			$data['class_id'] = $class_id;
			$data['action_name'] = $action_name;
			$db_table->insert($data);
			$template_id = $db_table->get_last_insert_id();
			
			foreach($access_data as $group_id => $rights)
			{
				$data = array();
      	$data['id'] = null;
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
	
	public function get_user_action_access_templates($class_id)
	{
		$db = db_factory :: instance();
		
		$sql = "SELECT 
		        stoat.action_name as action_name, 
		        stoat.class_id as class_id, 
		        stoati.template_id as template_id, 
		        stoati.user_id as user_id, 
		        stoati.w as w, 
		        stoati.r as r 
		        FROM sys_user_object_access_template as stoat, 
			      sys_user_object_access_template_item as stoati
			      WHERE stoat.class_id = {$class_id} AND
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

	public function get_group_action_access_templates($class_id)
	{
		$db = db_factory :: instance();
		
		$sql = "SELECT 
		        stoat.action_name as action_name, 
		        stoat.class_id as class_id, 
		        stoati.template_id as template_id, 
		        stoati.group_id as group_id, 
		        stoati.w as w, 
		        stoati.r as r
		        FROM sys_group_object_access_template as stoat, 
			      sys_group_object_access_template_item as stoati
			      WHERE stoat.class_id = {$class_id} AND
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

	public function get_user_action_access_template($class_id, $action_name)
	{
		$template = $this->get_user_action_access_templates($class_id);
		
		if (isset($template[$action_name]))
			return $template[$action_name];
		else
			return array();	
	}

	public function get_group_action_access_template($class_id, $action_name)
	{
		$template = $this->get_group_action_access_templates($class_id);
		
		if (isset($template[$action_name]))
			return $template[$action_name];
		else
			return array();	
	}
}
?>