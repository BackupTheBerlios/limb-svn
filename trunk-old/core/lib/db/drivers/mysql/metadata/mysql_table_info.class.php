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

require_once(LIMB_DIR . '/core/lib/db/metadata/table_info.class.php');

/**
* mysql implementation of table_info.
* 
*/
class mysql_table_info extends table_info
{
	/**
	* Loads the columns for this table.
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function init_columns()
	{
		include_once(LIMB_DIR . '/core/lib/db/metadata/column_info.class.php');
		include_once(LIMB_DIR . '/core/lib/db/drivers/mysql/mysql_types.class.php');

		if (!@mysql_select_db($this->dbname, $this->dblink))
		{
			return new sql_exception(DB_ERROR_NODBSELECTED, 'No database selected');
		} 
		// To get all of the attributes we need, we use
		// the mysql "SHOW COLUMNS FROM $tablename" SQL.  We cannot
		// use the API functions (e.g. mysql_list_fields() because they
		// do not return complete information -- e.g. precision / scale, default
		// values).
		$res = mysql_query("SHOW COLUMNS FROM " . $this->name, $this->dblink);

		$defaults = array();
		$native_types = array();
		$precisions = array();

		while ($row = mysql_fetch_assoc($res))
		{
			$name = $row['Field'];
			$default = $row['Default'];
			$is_nullable = ($row['Null'] == 'YES');

			$size = null;
			$precision = null;

			if (preg_match('/^(\w+)[\(]?([\d,]*)[\)]?$/', $row['Type'], $matches))
			{ 
				// colname[1]   size/precision[2]
				$native_type = $matches[1];
				if ($matches[2])
				{
					if (($cpos = strpos($matches[2], ',')) !== false)
					{
						$size = (int) substr($matches[2], 0, $cpos);
						$precision = (int) substr($matches[2], $cpos + 1);
					} 
					else
					{
						$size = (int) $matches[2];
					} 
				} 
			} 
			elseif (preg_match('/^(\w+)\(/', $row['Type'], $matches))
			{
				$native_type = $matches[1];
			} 
			else
			{
				$native_type = $row['Type'];
			} 

			$this->columns[$name] = new column_info($this, $name, mysql_types::get_type($native_type), $native_type, $size, $precision, $is_nullable, $default);
		} 

		$this->cols_loaded = true;
		return true;
	} 

	/**
	* Loads the primary key information for this table.
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function init_primary_key()
	{
		include_once(LIMB_DIR . '/core/lib/db/metadata/pk_info.class.php');
		// columns have to be loaded first
		if (!$this->cols_loaded) $this->init_columns();

		if (!@mysql_select_db($this->dbname, $this->dblink))
		{
			return new sql_exception(DB_ERROR_NODBSELECTED, 'No database selected');
		} 
		// Primary Keys
		$res = mysql_query("SHOW KEYS FROM " . $this->name, $this->dblink); 
		// Loop through the returned results, grouping the same key_name together
		// adding each column for that key.
		while ($row = mysql_fetch_assoc($res))
		{
			$name = $row["Column_name"];
			if (!isset($this->primary_key))
			{
				$this->primary_key = &new pk_info($name);
			} 
			$this->primary_key->add_column($this->columns[ $name ]);
		} 

		$this->primary_key_loaded = true;
		return true;
	} 

	/**
	* Loads the indexes for this table.
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function init_indexes()
	{
		include_once(LIMB_DIR . '/core/lib/db/metadata/index_info.class.php');
		// columns have to be loaded first
		if (!$this->cols_loaded) $this->init_columns();

		if (!@mysql_select_db($this->dbname, $this->dblink))
		{
			return new sql_exception(DB_ERROR_NODBSELECTED, 'No database selected');
		} 
		// Indexes
		$res = mysql_query("SHOW INDEX FROM " . $this->name, $this->dblink); 
		// Loop through the returned results, grouping the same key_name together
		// adding each column for that key.
		while ($row = mysql_fetch_assoc($res))
		{
			$name = $row["Column_name"];
			if (!isset($this->indexes[$name]))
			{
				$this->indexes[$name] = new index_info($name);
			} 
			$this->indexes[$name]->add_column($this->columns[ $name ]);
		} 

		$this->indexes_loaded = true;
		return true;
	} 

	/**
	* * Load foreign keys (unsupported in mysql).
	*/
	function init_foreign_keys()
	{ 
		// columns have to be loaded first
		if (!$this->cols_loaded)
		{
			if (($e = $this->init_columns()) !== true)
			{
				return $e;
			} 
		} 
		// Foreign keys are not supported in mysql.
		$this->fks_loaded = true;
		return true;
	} 
} 
