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

require_once(LIMB_DIR . 'core/template/component.class.php');
require_once(LIMB_DIR . 'core/lib/util/empty_dataset.class.php');

/**
* Represents list tags at runtime, providing an API for preparing the data set
*/
class list_component extends component
{
	/**
	* Data set to iterate over when rendering the list
	* 
	* @var object 
	* @access private 
	*/
	var $dataset;
	/**
	* Whether to show the list seperator
	* 
	* @var boolean 
	* @access private 
	*/
	var $show_separator;
	
	var $offset = 0;
	
	/**
	* Registers a dataset with the list component. The dataset must
	* implement the iterator methods defined in dataspace
	* 
	* @see dataspace
	* @param object $ implementing the dataspace iterator methods
	* @return void 
	* @access public 
	*/
	function register_dataset(&$dataset)
	{
		$this->dataset =& $dataset;
	}
	 
	// Temporary delegation until better solution can be found
	function get($name)
	{		
		return $this->dataset->get($name);
	} 

	function reset()
	{
		return $this->dataset->reset();
	} 

	function next()
	{
		return $this->dataset->next();
	} 

	function register_filter(&$filter)
	{
		$this->dataset->register_filter($filter);
	} 
	
	function get_by_index_string($raw_index)
	{
		return $this->dataset->get_by_index_string($raw_index);
	}
	
	function set_offset($offset)
	{
	  $this->offset = $offset;
	}
	
	function get_counter()
	{
	  return $this->dataset->get_counter() + $this->offset + 1;
	}

	/**
	* Prepares the list for iteration, creating an empty_dataset if no
	* data set has been registered then calling the dataset reset
	* method.
	* 
	* @see empty_dataset
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		if (empty($this->dataset))
		{
			$this->register_dataset(new empty_dataset());
		}
		 
		$this->dataset->reset(); 
		$this->dataset->prepare();
		
		$this->show_separator = false;		
	} 
} 

?>