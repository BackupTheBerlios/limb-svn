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

require_once(LIMB_DIR . 'class/lib/util/complex_array.class.php');

/**
* The dataspace is a container for a set of named data values (variables).
*/
class dataspace
{
	/**
	* Variables stored in the dataspace are contained here
	* 
	* @var array 
	* @access private 
	*/
	var $vars = array();
	/**
	* Filter object for transforming stored data
	* 
	* @var object 
	* @access private 
	*/
	var $filter;
	
	function dataspace($vars=null)
	{
		if(is_array($vars))
			$this->import($vars);
	}
	
	/**
	* Gets a copy of a stored variable by name
	* 
	* @param string $ name of variable
	* @return mixed value of variable or VOID if not found
	* @access public 
	*/
	function get($name)
	{ 
		// isset check is faster than error suppression operator (@)
		if (isset($this->vars[$name]))
		{
			return $this->vars[$name];
		} 
	} 
	
	function _process_index_string($index)
	{
		if(!preg_match('/^(\[\w+\]|\[\'\w+\'\]|\[\"\w+\"\])+$/', $index))
			return null;
		
		$index = str_replace(array('"', '\''), array('', ''), $index);
		$index = str_replace(array('[', ']'), array('["', '"]'), $index);
		return $index;
	}
	
	function get_by_index_string($raw_index)
	{
		if(!$index = $this->_process_index_string($raw_index))
			return null;
								
		eval('$res = isset($this->vars' . $index . ') ? $this->vars' . $index . ' : null;');
		
		return $res;
	}

	function set_by_index_string($raw_index, $value)
	{
		if(!$index = $this->_process_index_string($raw_index))
			return null;
										
		eval('$this->vars' . $index . ' = "";$res =& $this->vars' . $index . ';');
		$res = $value;
	}

	function get_size()
	{
		return count($this->vars);
	}
	
	/**
	* Stores a copy of a variable
	* 
	* @param string $ name of variable
	* @param mixed $ value of variable
	* @return void 
	* @access public 
	*/
	function set($name, $value)
	{
		$this->vars[$name] = $value;
	} 

	/**
	* Concatenates a value to the end of an existing variable
	* 
	* @param string $ name of variable
	* @param mixed $ value to append
	* @deprecated append is probably superflous now.  Remove?
	* @return void 
	* @access public 
	*/
	function append($name, $value)
	{
		$this->vars[$name] .= $value;
	} 

	/**
	* Places a copy of an array in the data store, using the 1st dimension
	* array keys as the variable names.
	* 
	* @param array $ 
	* @return void 
	* @access public 
	*/
	// Rename to replace?
	function import($valuelist)
	{
		$this->vars = $valuelist;
	} 
	// Reference counting?  huh?
	/**
	* Append a new list of values to the dataspace. Existing key values will be
	* overwritten if duplicated in the new value list.
	* 
	* @param array $ 
	* @return void 
	* @access public 
	*/
	// rename to import?
	function import_append($valuelist)
	{
		foreach ($valuelist as $key => $value)
		{
			$this->set($key, $value);
		} 
	} 
	
	function merge($valuelist)
	{
		if(is_array($valuelist) && sizeof($valuelist))
			$this->vars = complex_array :: array_merge($this->vars, $valuelist);
			
		if(!is_array($this->vars))
			$this->vars = array();
	}

	/**
	* Returns a reference to the complete array of variables stored
	* 
	* @return array 
	* @access public 
	*/
	function &export()
	{
		return $this->vars;
	} 

	/**
	* Registers a filter with the dataspace. Filters are used to transform
	* stored variables.
	* 
	* @param object $ instance of filter class containing a doFilter() method
	* @return void 
	* @access public 
	*/
	function register_filter(&$filter)
	{
		$this->filter = &$filter;
	} 

	/**
	* Prepares the dataspace, executing the doFilter() method of the
	* registered filter, if one exists
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		if (isset($this->filter))
		{
			$this->filter->do_filter($this->vars);
		} 
	} 
	
	function destroy($name)
	{
		if (isset($this->vars[$name]))
		{
		  unset($this->vars[$name]);
		} 
	}
	
	function reset()
	{
		$this->vars = array();
	}
	
	function is_empty()
	{
		return count($this->vars) ? false : true;
	}
} 

?>