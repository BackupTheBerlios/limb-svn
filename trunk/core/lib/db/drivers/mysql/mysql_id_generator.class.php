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

require_once(LIMB_DIR . '/core/lib/db/id_generator.class.php');
require_once(LIMB_DIR . '/core/lib/db/drivers/mysql/mysql_connection.class.php');

/**
* mysql id_generator implimenation.
* 
*/
class mysql_id_generator extends id_generator
{
	/**
	* * connection object that instantiated this class
	*/
	var $conn;

	/**
	* Creates a new id_generator class, saves passed connection for use
	* later by get_id() method.
	* 
	* @param connection $conn 
	*/
	function mysql_id_generator(&$conn)
	{
		if (! is_a($conn, 'connection'))
		{
			debug :: write_warning("parameter 1 not of type 'connection'",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 

		$this->conn = &$conn;
	} 

	/**
	* 
	* @see id_generator::is_before_insert()
	*/
	function is_before_insert()
	{
		return false;
	} 

	/**
	* 
	* @see id_generator::is_after_insert()
	*/
	function is_after_insert()
	{
		return true;
	} 

	/**
	* 
	* @see id_generator::get_id_method()
	*/
	function get_id_method()
	{
		return id_generator::AUTOINCREMENT();
	} 

	/**
	* 
	* @see id_generator::get_id()
	*/
	function get_id($unused = null)
	{
		return mysql_insert_id($this->conn->get_resource());
	} 
} 

