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

require_once(LIMB_DIR . 'core/model/search/full_text_indexer.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class full_text_search
{
	var $connection = null;
	var $indexer = null;
	var $use_boolean_mode = false;
	
	function full_text_search()
	{
		$this->connection=& db_factory :: get_connection();
		$this->indexer =& new full_text_indexer();
		
		$this->use_boolean_mode = $this->_check_boolean_mode();
	}
				
	function & find($query, $class_id=null)
	{	
		$result = array();
		
		if($query->is_empty())
			return $result;
		
		$sql = $this->_get_search_sql($query);
		
		if($class_id !== null)
			$sql .= " AND class_id={$class_id}";
		
		$result =& $this->_get_db_result($sql);
		
		return $result;
	}
	
	function & find_by_ids($ids, $query)
	{
		$result = array();
		
		if($query->is_empty())
			return $result;

		$sql = $this->_get_search_sql($query);
		
		$sql .= " AND " . sql_in('object_id', $ids);
		
		$result =& $this->_get_db_result($sql);
		
		return $result;
	}
	
	function _check_boolean_mode()
	{
		if(DB_TYPE == 'mysql')
		{
			$this->connection->sql_exec('SELECT VERSION() as version');
			$row = $this->connection->fetch_row();
			
			if(($pos = strpos($row['version'], '.')) !== false)
			{
				$version = (int)substr($row['version'], 0, $pos);
				
				if($version > 3)
					return true;
			}
		}	
		return false;
	}
	
	function _process_query($query_object)
	{
		$query_items = $query_object->get_query_items();
		
		foreach($query_items as $key => $data)
			$query_items[$key] = $this->connection->escape($data);
		
		if($this->use_boolean_mode)
		{
			$query = implode('* ', $query_items) . '*';
		}
		
		return $query;
	}
	
	function _get_search_sql($query_object)
	{
		$query = $this->_process_query($query_object);
		
		$boolean_mode = '';		
		if($this->use_boolean_mode)
			$boolean_mode = 'IN BOOLEAN MODE';
			
		$sql = sprintf('SELECT 
										object_id, 
										(MATCH (body) AGAINST ("%s" %s))*weight as score
										FROM sys_full_text_index
										WHERE MATCH (body) AGAINST ("%s" %s)',
										$query,
										$boolean_mode,
										$query,
										$boolean_mode										
									);
									
		return $sql;
	}
	
	function _get_db_result($sql)
	{
		$this->connection->sql_exec($sql);
		
		$result = array();
		while($row = $this->connection->fetch_row())
		{
			if(!isset($result[$row['object_id']]))
				$result[$row['object_id']] = $row['score'];
			else
				$result[$row['object_id']] += $row['score'];
		}

		arsort($result, SORT_NUMERIC);
		
		return $result;
	}
	
} 

?>
