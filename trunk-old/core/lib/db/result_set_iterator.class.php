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
* Basic result_set Iterator.
* 
* This can be returned by your class's get_iterator() method, but of course
* you can also implement your own (e.g. to get better performance, by using direct
* driver calls and avoiding other side-effects inherent in result_set scrolling
* functions -- e.g. before_first() / after_last(), etc.).
* 
* Important: result_set iteration does rewind the resultset if it is not at the
* start.  Not all drivers support reverse scrolling, so this may result in an
* exception in some cases (Oracle).
* 
* Developer note:
* The implementation of this class is a little weird because it fetches the
* array _early_ in order to answer has_more() w/o needing to know total num
* of fields.  Remember the way iterators work:
* <code>
* $it = $obj->get_iterator();
* for($it->rewind(); $it->has_more(); $it->next()) {
*   $key = $it->current();
*   $val = $it->key();
*   echo "$key = $val\n";
* }
* unset($it);
* </code>
* 
*/
class result_set_iterator
{
	var $rs;

	/**
	* Construct the iterator.
	* 
	* @param result_set $rs 
	*/
	function result_set_iterator(&$rs)
	{
		if (! is_a($rs, 'result_set'))
		{
			debug :: write_error("result_set_iterator::result_set_iterator(): parameter 1 not of type 'result_set' !",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 

		$this->rs = &$rs;
	} 

	/**
	* If not at start of resultset, this method will call seek(0).
	* 
	* @see result_set::seek()
	*/
	function rewind()
	{
		if (!$this->rs->is_before_first())
		{
			$this->rs->seek(0);
		} 
	} 

	/**
	* This method checks to see whether there are more results
	* by advancing the cursor position.
	* 
	* @see result_set::next()
	*/
	function &has_more()
	{
		return $this->rs->next();
	} 

	/**
	* Returns the cursor position.
	* 
	* @return int 
	*/
	function key()
	{
		return $this->rs->get_cursor_pos();
	} 

	/**
	* Returns the row (assoc array) at current cursor pos.
	* 
	* @return array 
	*/
	function &current()
	{
		return $this->rs->get_row();
	} 

	/**
	* This method does not actually do anything since we have already advanced
	* the cursor pos in has_more().
	* 
	* @see has_more
	*/
	function next()
	{
	} 
} 
