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

class array_dataset
{
	protected $data = array();
	
	protected $record = array();
	
	protected $first = true;
	
	protected $counter = 0;
	
	function __construct($array = null)
	{
		if (is_array($array))
		{
			$this->add_array($array);
		} 
	} 
	
	protected function _set_current_record()
	{
		if ($this->first)
			$this->next();
	} 
	
	protected function _save_current_record($record)
	{
		$this->data[key($this->data)] = $record;
	} 
	
	public function reset()
	{
		$this->first = true;
		$this->counter = 0;
	} 
	
	public function next()
	{
		if ($this->first)
		{
			$record = reset($this->data);
			$this->first = false;
			$this->counter = 0;
		} 
		else
		{
			$record = next($this->data);
			$this->counter++;
		} 
		if (is_array($record))
		{
			$this->record = $record;
			return true;
		} 
		else
		{
			$this->record = null;
			return false;
		} 
	} 
	
	public function get($name)
	{
		$this->_set_current_record();
		
		if (isset($this->record[$name]))
			return $this->record[$name];
	} 
	
	public function set($name, $value)
	{
		$this->_set_current_record();
		$this->record[$name] = $value;
		$this->_save_current_record($this->record);
	} 
	
	public function append($name, $value)
	{
		$this->_set_current_record();
		$this->record[$name] .= $value;
		$this->_save_current_record($this->record);
	} 
	
	public function import($valuelist)
	{
		$this->_set_current_record();
		if (is_array($valuelist))
		{
			$this->record = null;
			$this->record = $valuelist;
		} 
		$this->_save_current_record($this->record);
	} 
	
	public function import_append($valuelist)
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
	
	public function export()
	{
		$this->_set_current_record();
		return $this->record;
	} 
	
	public function add_array($array)
	{
		foreach ($array as $value)
		{
			if (is_array($value))
			{
				$this->data[] = $value;
			} 
		} 
	}
	
	public function get_counter()
	{
		return $this->counter;
	}
	
	public function get_total_row_count()
	{
		return sizeof($this->data);
	}
	
	public function get_by_index_string($index)
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