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
* Interface for classes that provide functionality to get SEQUENCE or AUTO-INCREMENT ids from the database.
*/
class id_generator
{
	/**
	* * SEQUENCE id generator type
	*/
	function SEQUENCE()
	{
		return(1);
	} 
	/**
	* * AUTO INCREMENT id generator type
	*/
	function AUTOINCREMENT()
	{
		return(2);
	} 

	/**
	* Convenience method that returns TRUE if id is generated
	* before an INSERT statement.  This is the same as checking
	* whether the generator type is SEQUENCE.
	* 
	* @return boolean TRUE if gen id method is SEQUENCE
	* @see get_id_method
	*/
	function is_before_insert()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Convenience method that returns TRUE if id is generated
	* after an INSERT statement.  This is the same as checking
	* whether the generator type is AUTOINCREMENT.
	* 
	* @return boolean TRUE if gen id method is AUTOINCREMENT
	* @see get_id_method
	*/
	function is_after_insert()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get the preferred type / style for generating ids for RDBMS.
	* 
	* @return int SEQUENCE or AUTOINCREMENT
	*/
	function get_id_method()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get the autoincrement or sequence id given the current connection
	* and any additional needed info (e.g. sequence name for sequences).
	* <p>
	* Note: if you take advantage of the fact that $key_info may not be specified
	* you should make sure that your code is setup in such a way that it will
	* be portable if you change from an RDBMS that uses AUTOINCREMENT to one that
	* uses SEQUENCE (i.e. in which case you would need to specify sequence name).
	* 
	* @param mixed $key_info Any additional information (e.g. sequence name) needed to fetch the id.
	* @return int The last id / next id.
	*/
	function get_id($key_info = null)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 

