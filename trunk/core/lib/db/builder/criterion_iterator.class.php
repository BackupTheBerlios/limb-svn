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

/**
* Class that implements SPL Iterator interface.  This allows foreach() to
* be used w/ Criteria objects.  Probably there is no performance advantage
* to doing it this way, but it makes sense -- and simpler code.
* 
* (inspired by propel project http://propel.phpdbg.org)
*/
class criterion_iterator
{
	var $idx = 0;
	var $criteria;
	var $criteria_keys;
	var $criteria_size;

	function criterion_iterator(&$criteria)
	{
		$this->criteria = &$criteria;
		$this->criteria_keys = $criteria->keys();
		$this->criteria_size = count($this->criteria_keys);
	} 
	
	function count()
	{
		return $this->criteria_size;
	}

	function rewind()
	{
		$this->idx = 0;
	} 

	function valid()
	{
		return $this->idx < $this->criteria_size;
	} 

	function key()
	{
		return $this->criteria_keys[$this->idx];
	} 

	function &current()
	{
		return $this->criteria->get_criterion($this->criteria_keys[$this->idx]);
	} 

	function next()
	{
		$this->idx++;
	} 
} 

?>