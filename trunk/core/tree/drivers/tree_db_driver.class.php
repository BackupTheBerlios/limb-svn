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
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/tree/drivers/tree_driver.class.php');

class tree_db_driver extends tree_driver
{
	var $connection = null;

	/**
	* 
	* @var array The field parameters of the table with the nested set.
	* @access public 
	*/
	var $_params = array(
		'id' => 'id',
		'root_id' => 'root_id',
		'identifier' => 'identifier',
		'object_id' => 'object_id',
		'level' => 'level', 
		'parent_id' => 'parent_id',
	);
	
	/**
	* 
	* @var array An array of field ids that must exist in the table
	* @access private 
	*/
	var $_required_params = array('id', 'root_id', 'level');
	
	/**
	* 
	* @var string The table with the actual tree data
	* @access public 
	*/
	var $_node_table = 'sys_site_object_tree';

	function tree_db_driver()
	{
		$this->connection=& db_factory :: get_connection();
		
		parent :: tree_driver();
	}
	
	function set_node_table($table_name)
	{
		$this->_node_table = $table_name;
	}
	
	function & _get_result_set($sql)
	{
		$this->connection->sql_exec($sql);
		$nodes =& $this->connection->get_array('id');

		return $nodes;
	} 
	
	function _assign_result_set(&$nodes, $sql)
	{
		$this->_sql = $sql;
		$this->connection->sql_exec($sql);
		$this->connection->assign_array($nodes, 'id');
	} 
	
	/**
	* Changes the payload of a node
	* 
	* @param int $id Node ID
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @access public 
	* @return bool True if the update is successful
	*/
	function update_node($id, $values)
	{
		if(!$this->is_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		
		$this->_verify_user_values($values);
				
		return $this->connection->sql_update($this->_node_table, $values, array('id' => $id));
	} 
	
	/**
	* Adds a specific type of SQL to a sql_exec string
	* 
	* @param array $add_sql The array of SQL strings to add.  Example value:
	*                $add_sql = array(
	*                'columns' => 'tb2.col2, tb2.col3',         // Additional tables/columns
	*                'join' => 'LEFT JOIN tb1 USING(id)', // Join statement
	*                'append' => 'GROUP by tb1.id');      // Group condition
	* @param string $type The type of SQL.  Can be 'columns', 'join', or 'append'.
	* @access private 
	* @return string The SQL, properly formatted
	*/
	function _add_sql($add_sql, $type)
	{
		if (!isset($add_sql[$type]))
			return '';

		return implode(' ', $add_sql[$type]);
	} 
	
	function _is_table_joined($table_name, $add_sql)
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
	* 
	* @access private 
	* @return string A string of sql_exec fields to select
	*/
	function _get_select_fields()
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
	* 
	* @var string $caller The calling method
	* @var string $values The values array
	* @access private 
	* @return void 
	*/
	function _verify_user_values(&$values)
	{
		if ($this->_dumb_mode)
			return true;

		foreach($values as $field => $value)
		{
			if (!isset($this->_params[$field]))
			{
	    	debug :: write_error(TREE_ERROR_NODE_WRONG_PARAM,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
	    		 array('param' => $field)
	    	);
				unset($values[$field]);
				continue;
			}
			 
			if (in_array($this->_params[$field], $this->_required_params))
			{
	    	debug :: write_error(TREE_ERROR_NODE_WRONG_PARAM,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		 array('value' => $field)
	    	);

				unset($values[$field]);
			} 
		} 
	} 
}

?>