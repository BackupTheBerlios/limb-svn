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
require_once(LIMB_DIR . '/class/core/permissions/authorizer.interface.php');

class simple_authorizer implements authorizer
{
  protected $_cached_class_accessible_actions = array();

  public function get_accessible_object_ids($object_ids, $action = 'display', $class_id = null)
  {
		if (!count($object_ids))
			return array();
			
		$in_ids = implode(',', $object_ids);

    $accessor_ids = implode(',', $this->_get_accessor_ids());
    
    if (!$accessor_ids)
      return array();
			
		$db = db_factory :: instance();
		
		if ($class_id)	
		{
  		$sql = "SELECT soa.object_id as id
  			FROM sys_site_object as sso, sys_object_access as soa
  			WHERE soa.object_id IN ({$in_ids})
  			AND sso.id = soa.object_id AND sso.class_id = {$class_id}";
  	}
		else
  		$sql = "SELECT soa.object_id as id
  			FROM sys_object_access as soa
  			WHERE soa.object_id IN ({$in_ids})";

		$sql	.= " AND soa.accessor_id IN ({$accessor_ids})";
		
		$sql .=" AND soa.access = 1";
	
  	$db->sql_exec($sql);
  	
  	return array_keys($db->get_array('id'));
  }

	protected function _get_accessor_ids()
	{
		$accessor_ids = array();
		
		$user = LimbToolsBox :: getToolkit()->getUser();
		
		if(($user_id = $user->get_id()) != user :: DEFAULT_USER_ID)
			$accessor_ids[] = $user_id;
		
		$groups = $user->get('groups', array());
		foreach(array_keys($groups) as $group_id)	
			$accessor_ids[] = $group_id;
		
		return $accessor_ids;	
	}
  

  public function assign_actions_to_objects(&$objects_data)
  {
		if(isset($objects_data['id']))//hack which allows to accept objects arrays and single objects
			$arr[] =& $objects_data;
		else
			$arr =& $objects_data;
  
		$controllers = array();
		$class_actions = array();
				
		foreach($arr as $key => $data)
		{
			$class_id = $data['class_id'];
			
			$arr[$key]['actions'] = array();
						
			if (!isset($class_actions[$class_id]))
			{
				$site_object_controller = $this->_get_controller($data['class_name']);
				$class_actions[$class_id] = $site_object_controller->get_actions_definitions();
			}	

			$accessible_actions = $this->_get_class_accessible_actions($class_id);

			foreach($class_actions[$class_id] as $action_name => $action_params)
			{
			  if (isset($accessible_actions[$action_name]))
					$arr[$key]['actions'][$action_name] = $action_params;
			}			
		}
  }

	//for mocking
	protected function _get_controller($class_name)
	{
		return LimbToolsBox :: getToolkit()->createSiteObject($class_name)->get_controller();
	}

	protected function _get_class_accessible_actions($class_id)
	{
	  if(isset($this->_cached_class_accessible_actions[$class_id]))
	    return $this->_cached_class_accessible_actions[$class_id];
	    
		$accessor_ids = $this->_get_accessor_ids();
		
		$in_ids = implode(',', $accessor_ids);

		$db = db_factory :: instance();

		$sql = "SELECT saa.action_name as action_name FROM sys_action_access as saa
			WHERE saa.class_id = {$class_id} AND
			saa.accessor_id IN ({$in_ids})
			GROUP BY saa.action_name";
		
    $db->sql_exec($sql);
    $result = $db->get_array('action_name');
		
		$this->_cached_class_accessible_actions[$class_id] = $result;
		return $result;	
	}
}
?>