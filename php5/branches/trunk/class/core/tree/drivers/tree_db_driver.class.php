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
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/core/tree/drivers/tree_driver.class.php');

abstract class tree_db_driver extends tree_driver
{
	protected $_db = null;

	protected $_params = array(
		'id' => 'id',
		'root_id' => 'root_id',
		'identifier' => 'identifier',
		'object_id' => 'object_id',
		'level' => 'level', 
		'parent_id' => 'parent_id',
	);
	
	protected $_required_params = array('id', 'root_id', 'level');
	
	protected $_node_table = 'sys_site_object_tree';

	function __construct()
	{
		$this->_db = db_factory :: instance();
	}
	
	public function set_node_table($table_name)
	{
		$this->_node_table = $table_name;
	}
	
	public function get_node_table()
	{
		return $this->_node_table;
	}
	
	public function _get_result_set($sql)
	{
		$this->_db->sql_exec($sql);
		return $this->_db->get_array('id');
	} 
	
	protected function _assign_result_set(&$nodes, $sql)
	{
		$this->_sql = $sql;
		$this->_db->sql_exec($sql);
		$this->_db->assign_array($nodes, 'id');
	} 
	
	/**
	* Changes the payload of a node
	*/
	public function update_node($id, $values, $internal = false)
	{
		if(!$this->is_node($id))
		{
    	return false;
		} 
		
		if($internal === false)
		  $this->_verify_user_values($values);
				
		return $this->_db->sql_update($this->_node_table, $values, array('id' => $id));
	} 
	
	/**
	* Adds a specific type of SQL to a sql_exec string
	*/
	protected function _add_sql($add_sql, $type)
	{
		if (!isset($add_sql[$type]))
			return '';

		return implode(' ', $add_sql[$type]);
	} 
	
	protected function _is_table_joined($table_name, $add_sql)
	{
		if(!isset($add_sql['join']))
			return false;
			
		foreach($add_sql['join'] as $sql)
		{
			if(strpos($sql, $table_name) !== false)
				return true;
		}
		return false;
	}

	/**
	* Gets the select fields based on the params
	*/
	protected function _get_select_fields()
	{
		$sql_exec_fields = array();
		foreach ($this->_params as $key => $val)
		{
			$sql_exec_fields[] = $this->_node_table . '.' . $key . ' AS ' . $val;
		} 

		return implode(', ', $sql_exec_fields);
	} 
  
	/**
	* Clean values from protected or unknown columns
	*/
	protected function _verify_user_values(&$values)
	{
		if ($this->_dumb_mode)
			return true;

		foreach($values as $field => $value)
		{
			if (!isset($this->_params[$field]))
			{
			  unset($values[$field]);
				continue;
			}
			 
			if (in_array($this->_params[$field], $this->_required_params))
			{
			  unset($values[$field]);
			} 
		} 
	} 
}

?>