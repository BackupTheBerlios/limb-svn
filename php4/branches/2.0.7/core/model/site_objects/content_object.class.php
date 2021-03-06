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

require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class content_object extends site_object
{
	var $_db_table = null;
	
	function content_object()
	{		
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'icon' => '/shared/images/generic.gif'
		);
	}
		
	function & _get_db_table()
	{
		if(!$this->_db_table)
		{
			$db_table_name = $this->_get_db_table_name();
				
			$this->_db_table =& db_table_factory :: instance($db_table_name);
		}	
			
		return $this->_db_table;
	}
	
	function _get_db_table_name()
	{
		return 
		isset($this->_class_properties['db_table_name']) ? 
		$this->_class_properties['db_table_name'] : 
		get_class($this);
	}
	
	function & fetch($params=array(), $sql_params=array())
	{
		$sql_params['columns'][] = ', tn.*, tn.id as record_id';
		
		$db_table =& $this->_get_db_table();
		$table_name = $db_table->get_table_name();
		$sql_params['tables'][] = ",{$table_name} as tn";
		
		$sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
		
		$result =& parent :: fetch($params, $sql_params);
		
		return $result;
	}
	
	function & fetch_ids($params=array(), $sql_params=array(), $sort_ids = array())
	{
		$db_table =& $this->_get_db_table();
		$table_name = $db_table->get_table_name();
		$sql_params['tables'][] = ",{$table_name} as tn";
		
		$sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
		
		$result =& parent :: fetch_ids($params, $sql_params, $sort_ids);
		
		return $result;
	}

	function fetch_count($params=array(), $sql_params=array())
	{
		$db_table =& $this->_get_db_table();
		$table_name = $db_table->get_table_name();
		$sql_params['tables'][] = ",{$table_name} as tn";
		
		$sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
		
		return parent :: fetch_count($params, $sql_params);
	}
		
	function _create_version_record()
	{
		$version_db_table =& db_table_factory :: instance('sys_object_version');
		
		$time = time();
		
		$data['object_id'] = $this->get_id();
		$data['version'] = $this->get_version();
		$data['created_date'] = $time;
		$data['modified_date'] = $time;
		$data['creator_id'] = user :: get_id();
		
		$version_db_table->insert($data);
		
		return true;
	}
			
	function _create_versioned_content_record()
	{
		$data = $this->_attributes->export();
		
		$data['object_id'] = $this->get_id();
		unset($data['id']);
				
		$db_table =& $this->_get_db_table();
		$db_table->insert($data);
		
		$record_id = $db_table->get_last_insert_id();
		$this->set_attribute('record_id', $record_id);
		
		return true;
	}
	
	function _update_versioned_content_record()
	{
		$data['version'] = $this->get_version();
		$data['object_id'] = $this->get_id();

		$db_table =& $this->_get_db_table();
		
		$row = current($db_table->get_list($data));
		
		if($row === false)
		{
			debug :: write_error('content record not found',
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
	    		$data);
	    	
	    return false;
	   }
		
		$id = $row['id'];

		$data = $this->_attributes->export();
		unset($data['id']);

		if($db_table->update_by_id($id, $data))
			return $id;
		else
		{
    	debug :: write_error('update failed',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id));
			return false;
		}
	}
	
	function update($force_create_new_version = true)
	{
		if(!parent :: update($force_create_new_version))
			return false;
		
		if ($force_create_new_version)
		{
			if(!$this->_create_version_record())
				return false;
		
			if($this->_create_versioned_content_record() !== false)
				return true;
			
			 debug :: write_error('creation of versioned record failed',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    		
    	return false;
		}
		
		return $this->_update_versioned_content_record();
	}
	
	function create($is_root = false)
	{
		if(($id = parent :: create($is_root)) === false)
			return false;
			
		if (!$this->_create_version_record())
			return false;
		
		if(!$this->_create_versioned_content_record())
			return false;
		
		return $id;
	}
				
	function delete()
	{
		if(!parent :: delete())
			return false;

		return $this->_delete_versioned_content_records();
	}

	function _delete_versioned_content_records()
	{
		$db_table =& $this->_get_db_table();	
		$db_table->delete(array('object_id' => $this->get_id()));
		
		return true;
	}	
}

?>