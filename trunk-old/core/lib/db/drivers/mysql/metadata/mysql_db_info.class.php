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

require_once(LIMB_DIR . '/core/lib/db/metadata/db_info.class.php');

/**
* mysql implementation of db_info.
* 
*/
class mysql_db_info extends db_info
{
	/**
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function init_tables()
	{
		include_once(LIMB_DIR . '/core/lib/db/drivers/mysql/metadata/mysql_table_info.class.php');

		$result = mysql_list_tables($this->dbname, $this->dblink);

		if (!$result)
		{
			return new sql_exception(DB_ERROR, "Could not list tables", mysql_error($this->dblink));
		} 

		while ($row = mysql_fetch_row($result))
		{
			$this->tables[strtoupper($row[0])] =& new mysql_table_info($this, $row[0]);
		} 

		return true;
	} 

	/**
	* mysql does not support sequences.
	* 
	* @return void 
	* @throws sql_exception
	*/
	function init_sequences()
	{ 
		// return throw (new sql_exception("mysql does not support sequences natively."));
	} 
} 
