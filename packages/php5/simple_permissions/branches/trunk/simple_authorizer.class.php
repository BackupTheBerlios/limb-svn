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
  protected $_cached_behaviour_accessible_actions = array();

  public function get_accessible_object_ids($object_ids, $action = 'display')
  {
		if (!count($object_ids))
			return array();
			
		$in_ids = implode(',', $object_ids);

    $accessor_ids = implode(',', $this->_get_accessor_ids());
    
    if (!$accessor_ids)
      return array();
			
		$db = Limb :: toolkit()->getDB();
		
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
		
		$user = Limb :: toolkit()->getUser();
		
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
		$behaviour_actions = array();
				
		foreach($arr as $key => $data)
		{
			$behaviour_name = $data['behaviour'];
			
			$arr[$key]['actions'] = array();
						
			if (!isset($behaviour_actions[$behaviour_name]))
			{
				$behaviour = $this->_get_behaviour($behaviour_name);
				$behaviour_actions[$behaviour_name] = $behaviour->get_actions_list();
			}	

			$accessible_actions = $this->_get_behaviour_accessible_actions($behaviour_name);

			foreach($accessible_actions[$behaviour_name] as $action_name)
			{
			  if (isset($behaviour_actions[$behaviour_name][$action_name]))
        {
          $method = 'get_' . $action_name . '_action_properties'; 
					$arr[$key]['actions'][$action_name] = $behaviour->$method();
        }  
			}			
		}
  }

	//for mocking
	protected function _get_behaviour($behaviour)
	{
		return Limb :: toolkit()->getBehaviour($behaviour);
	}

	protected function _get_behaviour_accessible_actions($behaviour_id)
	{
	  if(isset($this->_cached_behaviour_accessible_actions[$behaviour_id]))
	    return $this->_cached_behaviour_accessible_actions[$behaviour_id];
	    
		$accessor_ids = $this->_get_accessor_ids();
		
		$in_ids = implode(',', $accessor_ids);

		$db = Limb :: toolkit()->getDB();

		$sql = "SELECT saa.action_name as action_name FROM sys_action_access as saa
			WHERE saa.behaviour_id = {$behaviour_id} AND
			saa.accessor_id IN ({$in_ids})
			GROUP BY saa.action_name";
		
    $db->sql_exec($sql);
    $result = $db->get_array('action_name');
		
		$this->_cached_behaviour_accessible_actions[$behaviour_id] = $result;
		return $result;	
	}
}
?>