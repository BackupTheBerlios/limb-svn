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

require_once(LIMB_DIR . 'class/model/search/full_text_indexer.class.php');
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');

class full_text_search
{
	var $db = null;
	var $indexer = null;
	var $use_boolean_mode = false;
	
	function full_text_search()
	{
		$this->db =& db_factory :: instance();
		$this->indexer =& new full_text_indexer();
		
		$this->use_boolean_mode = $this->_check_boolean_mode();
	}
	

	function _can_perform_fulltext_search()
	{
		if(DB_TYPE == 'mysql')
		{
			$this->db->sql_exec('SELECT VERSION() as version');
			$row = $this->db->fetch_row();
			
			$version = explode('.', $row['version']);
			
			if((int)$version[0] > 3 || ((int)$version[0] == 3 && (int)$version[1] >= 23))
				return true;
		}	
		return false;
	}
	
	function & find($query, $class_id=null, $restricted_classes_ids = array(), $allowed_classes_ids = array())
	{	
		if(!$this->_can_perform_fulltext_search())
			$result = array();
		
		if($query->is_empty())
			return $result;
		
		$sql = $this->_get_search_sql($query);
		
		if (!$sql)
			return array();
		if($class_id !== null)
			$sql .= " AND class_id={$class_id}";
		else
		{
			if(count($restricted_classes_ids))	
				$sql .= ' AND NOT(' . sql_in('class_id', $restricted_classes_ids) . ')';
			if(count($allowed_classes_ids))	
				$sql .= ' AND ' . sql_in('class_id', $allowed_classes_ids);
		}
		
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
			$this->db->sql_exec('SELECT VERSION() as version');
			$row = $this->db->fetch_row();
			
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
		$query = '';
		
		$query_items = $query_object->get_query_items();
		
		foreach($query_items as $key => $data)
			$query_items[$key] = $this->db->escape($data);
		
		if($this->use_boolean_mode)
		{
			$query = implode('* ', $query_items) . '*';
		}
		else
		{
			$query = implode(' ', $query_items);
		}
		
		return $query;
	}
	
	function _get_search_sql($query_object)
	{
		$query = $this->_process_query($query_object);
		
		if(!$query)
			return '';
		
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
		$this->db->sql_exec($sql);
		
		$result = array();
		while($row = $this->db->fetch_row())
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
