<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: array_dataset.class.php 456 2004-02-16 18:52:50Z server $
*
***********************************************************************************/ 
/**
* Provides DataSpace combatible Iterator an array of associative arrays
* - a record set
*/
class array_dataset
{
	/**
	* Array to iterate over
	* 
	* @var array of arrays
	* @access private 
	*/
	var $data = array();
	/**
	* Current record (row) from the array
	* 
	* @var mixed 
	* @access private 
	*/
	var $record = array();
	/**
	* TRUE if at start of the array
	* 
	* @var boolean (default=true)
	* @access private 
	*/
	var $first = true;
	/**
	* Constructs array_data_set
	* 
	* @param array $ (an array of arrays)
	* @access public 
	*/
	
	var $counter = 0;
	
	function array_dataset($array = null)
	{
		if (is_array($array))
		{
			$this->add_array($array);
		} 
	} 
	/**
	* Badly named utility function to make sure that record points to something
	* - really could do with a more apt name, hint hint
	* 
	* @access private 
	* @return void 
	*/
	function &_set_current_record()
	{
		if ($this->first)
		{
			$this->next();
		} 
	} 
	/**
	* Saves the current, modified, record back into the array_data_set.
	* 
	* @access private 
	* @return void 
	* @param array $ The modified record
	*/
	function _save_current_record(&$record)
	{
		$this->data[key($this->data)] = &$record;
	} 
	/**
	* Returns to start of array_data_set
	* 
	* @return void 
	* @access public 
	*/
	function reset()
	{
		$this->first = true;
		$this->counter = 0;
	} 
	/**
	* Iterates through the array_data_set, setting $this->record to the current
	* record.
	* Returns TRUE if there was another record to iterator over
	* Returns false if it's reached the end of the array
	* 
	* @return boolean 
	* @access public 
	*/
	function next()
	{
		if ($this->first)
		{
			$record =& reset($this->data);
			$this->first = false;
			$this->counter = 0;
		} 
		else
		{
			$record =& next($this->data);
			$this->counter++;
		} 
		if (is_array($record))
		{
			$this->record =& $record;
			$this->prepare();
			return true;
		} 
		else
		{
			$this->record = null;
			return false;
		} 
	} 
	/**
	* Gets an element from the current record, given its name
	* 
	* @return mixed 
	* @access public 
	*/
	function get($name)
	{
		$this->_set_current_record();
		
		if (isset($this->record[$name]))
			return $this->record[$name];
	} 
	/**
	* Places an element in the current record of the array_data_set
	* 
	* @param string $ name of element
	* @param mixed $ value of element
	* @return void 
	* @access public 
	*/
	function set($name, $value)
	{
		$this->_set_current_record();
		$this->record[$name] = $value;
		$this->_save_current_record($this->record);
	} 
	/**
	* Appends some data to an existing element
	* 
	* @param string $ name of element
	* @param mixed $ value of element
	* @return void 
	* @access public 
	*/
	function append($name, $value)
	{
		$this->_set_current_record();
		$this->record[$name] .= $value;
		$this->_save_current_record($this->record);
	} 
	/**
	* Replaces the current record with a new array
	* 
	* @param array $ 
	* @return void 
	* @access public 
	*/
	function import($valuelist)
	{
		$this->_set_current_record();
		if (is_array($valuelist))
		{
			$this->record = null;
			$this->record = $valuelist;
		} 
		$this->_save_current_record($this->record);
	} 
	/**
	* Appends an array to the existing record. Duplicate keys will be
	* overwritten.
	* 
	* @param array $ 
	* @return void 
	* @access public 
	*/
	function import_append($valuelist)
	{
		if (is_array($valuelist))
		{
			$this->_set_current_record();
			foreach ($valuelist as $name => $value)
			{
				$this->set($name, $value);
			} 
			$this->_save_current_record($this->record);
		} 
	} 
	/**
	* Returns the complete data set
	* 
	* @return array 
	* @access public 
	*/
	function &export()
	{
		$this->_set_current_record();
		return $this->record;
	} 
	/**
	* Registers a filter.
	* Filters are used to transform stored variables.
	* 
	* @param object $ instance of filter class containing a do_filter() method
	* @return void 
	* @access public 
	*/
	function register_filter(&$filter)
	{
		$this->filter = &$filter;
	} 
	/**
	* Executes the do_filter() method of the
	* registered filter, if one exists
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		if (isset($this->filter))
		{
			$this->_set_current_record();
			$this->filter->do_filter($this->record);
		} 
	} 
	/**
	* Add an array to the current array_data_set. Keys are replaced with the next
	* available index for $this->data - duplicate keys will NOT be overwritten.
	* 
	* @return void 
	* @access public 
	* @param array $ 
	*/
	function add_array($array)
	{
		foreach ($array as $value)
		{
			if (is_array($value))
			{
				$this->data[] = $value;
			} 
		} 
	}
	
	function get_counter()
	{
		return $this->counter;
	}
	
	function get_total_row_count()
	{
		return sizeof($this->data);
	}
	
	function get_by_index_string($index)
	{
		$this->_set_current_record();
		
		if(!preg_match('/^(\[\w+\]|\[\'\w+\'\]|\[\"\w+\"\])+$/', $index))
			return '';
		
		$index = str_replace(array('"', '\''), array('', ''), $index);
		$index = str_replace(array('[', ']'), array('["', '"]'), $index);
					
		eval('$res = isset($this->record' . $index . ') ? $this->record' . $index . ' : "";');
		
		return $res;
	}
} 

?>