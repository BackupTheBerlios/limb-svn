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

require_once(LIMB_DIR . '/core/lib/db/peer/map/column_map.class.php');
require_once(LIMB_DIR . '/core/lib/db/peer/map/validator_map.class.php');

/**
* table_map is used to model a table in a database.
*/
class table_map
{
	/**
	* * The columns in the table.
	*/
	var $columns;

	/**
	* * The database this table belongs to.
	*/
	var $db_map;

	/**
	* * The name of the table.
	*/
	var $table_name;

	/**
	* * The PHP name of the table.
	*/
	var $php_name;

	/**
	* * The prefix on the table name.
	*/
	var $prefix;

	/**
	* * Whether to use an id generator for pkey.
	*/
	var $use_id_generator;

	/**
	* Object to store information that is needed if the
	* for generating primary keys.
	*/
	var $pk_info;

	/**
	* Constructor.
	* 
	* @param string $table_name The name of the table.
	* @param database_map $containing_db A database_map that this table belongs to.
	*/
	function table_map($table_name, &$containing_db)
	{
		$this->table_name = &$table_name;
		$this->db_map = &$containing_db;
		$this->columns = array();
	} 

	/**
	* Normalizes the column name, removing table prefix and uppercasing.
	* 
	* @param string $name 
	* @return string Normalized column name.
	*/
	function normalize_col_name($name)
	{
		if (false !== ($pos = strpos($name, '.')))
		{
			$name = substr($name, $pos + 1);
		} 
		$name = strtoupper($name);
		return $name;
	} 

	/**
	* Does this table contain the specified column?
	* 
	* @param  $name A String with the name of the column.
	* @return boolean True if the table contains the column.
	*/
	function contains_column($name)
	{
		if (!is_string($name))
		{
			$name = $name->get_column_name();
		} 
		return isset($this->columns[$this->normalize_col_name($name)]);
	} 

	/**
	* Get the database_map containing this table_map.
	* 
	* @return database_map A database_map.
	*/
	function &get_database_map()
	{
		return $this->db_map;
	} 

	/**
	* Get the name of the Table.
	* 
	* @return string A String with the name of the table.
	*/
	function get_name()
	{
		return $this->table_name;
	} 

	/**
	* Get the PHP name of the Table.
	* 
	* @return string A String with the name of the table.
	*/
	function get_php_name()
	{
		return $this->php_name;
	} 

	/**
	* Set the PHP name of the Table.
	* 
	* @param string $php_name The PHP Name for this table
	*/
	function set_php_name($php_name)
	{
		$this->php_name = $php_name;
	} 

	/**
	* Get table prefix name.
	* 
	* @return string A String with the prefix.
	*/
	function get_prefix()
	{
		return $this->prefix;
	} 

	/**
	* Set table prefix name.
	* 
	* @param string $prefix The prefix for the table name (ie: SCARAB for
	* SCARAB_PROJECT).
	* @return void 
	*/
	function set_prefix($prefix)
	{
		$this->prefix = $prefix;
	} 

	/**
	* Whether to use Id generator for primary key.
	* 
	* @return boolean 
	*/
	function is_use_id_generator()
	{
		return $this->use_id_generator;
	} 

	/**
	* Get the information used to generate a primary key
	* 
	* @return An Object.
	*/
	function &get_primary_key_method_info()
	{
		return $this->pk_info;
	} 

	/**
	* Get a column_map[] of the columns in this table.
	* 
	* @return array A column_map[].
	*/
	function &get_columns()
	{
		return $this->columns;
	} 

	/**
	* Get a column_map for the named table.
	* 
	* @param string $name A String with the name of the table.
	* @return column_map A column_map.
	*/
	function &get_column($name)
	{
		$name = $this->normalize_col_name($name);
		if (isset($this->columns[$name]))
		{
			return $this->columns[$name];
		} 
		return null;
	} 

	/**
	* Add a primary key column to this Table.
	* 
	* @param string $column_name A String with the column name.
	* @param string $type A string specifying the PHP native type.
	* @param int $creole_type The integer representing the Creole type.
	* @param  $size An int specifying the size.
	* @return column_map Newly added primary_key column.
	*/
	function &add_primary_key($column_name, $php_name, $type, $creole_type, $size = null)
	{
		return $this->add_column($column_name, $php_name, $type, $creole_type, $size, true, null, null);
	} 

	/**
	* Add a foreign key column to the table.
	* 
	* @param string $column_name A String with the column name.
	* @param string $type A string specifying the PHP native type.
	* @param int $creole_type The integer representing the Creole type.
	* @param string $fk_table A String with the foreign key table name.
	* @param string $fk_column A String with the foreign key column name.
	* @param int $size An int specifying the size.
	* @return column_map Newly added foreign_key column.
	*/
	function &add_foreign_key($column_name, $php_name, $type, $creole_type, $fk_table, $fk_column, $size = 0)
	{
		return $this->add_column($column_name, $php_name, $type, $creole_type, $size, false, $fk_table, $fk_column);
	} 

	/**
	* Add a foreign primary key column to the table.
	* 
	* @param string $column_name A String with the column name.
	* @param string $type A string specifying the PHP native type.
	* @param int $creole_type The integer representing the Creole type.
	* @param string $fk_table A String with the foreign key table name.
	* @param string $fk_column A String with the foreign key column name.
	* @param int $size An int specifying the size.
	* @return column_map Newly created foreign pkey column.
	*/
	function &add_foreign_primary_key($column_name, $php_name, $type, $creole_type, $fk_table, $fk_column, $size = 0)
	{
		return $this->add_column($column_name, $php_name, $type, $creole_type, $size, true, $fk_table, $fk_column);
	} 

	/**
	* Add a pre-created column to this table.  It will replace any
	* existing column.
	* 
	* @param column_map $cmap A column_map.
	* @return column_map The added column map.
	*/
	function &add_configured_column(&$cmap)
	{
		$this->columns[ $cmap->get_column_name() ] = &$cmap;
		return $cmap;
	} 

	/**
	* Add a column to the table.
	* 
	* @param string $ name A String with the column name.
	* @param string $type A string specifying the PHP native type.
	* @param int $creole_type The integer representing the Creole type.
	* @param int $size An int specifying the size.
	* @param boolean $pk True if column is a primary key.
	* @param string $fk_table A String with the foreign key table name.
	* @param  $fk_column A String with the foreign key column name.
	* @return column_map The newly created column.
	*/
	function &add_column($name, $php_name, $type, $creole_type, $size = null, $pk = null, $fk_table = null, $fk_column = null)
	{
		$col = &new column_map($name, $this);

		if ($fk_table && $fk_column)
		{
			if (substr($fk_column, '.') > 0 && substr($fk_column, $fk_table) !== false)
			{
				$fk_column = substr($fk_column, strlen($fk_table) + 1);
			} 
			$col->set_foreign_key($fk_table, $fk_column);
		} 

		$col->set_type($type);
		$col->set_creole_type($creole_type);
		$col->set_primary_key($pk);
		$col->set_size($size);
		$col->set_php_name($php_name);
		$this->columns[$name] = $col;

		return $this->columns[$name];
	} 

	/**
	* Add a validator to a table's column
	* 
	* @param string $column_name The name of the validator's column
	* @param string $name The rule name of this validator
	* @param string $classname The dot-path name of class to use (e.g. myapp.propel.my_validator)
	* @param string $value 
	* @param string $message The error message which is returned on invalid values
	* @return void 
	*/
	function &add_validator($column_name, $name, $classname, $value, $message)
	{
		$col = &$this->get_column($column_name);

		if ($col !== null)
		{
			$validator = &new validator_map($col);
			$validator->set_name($name);
			$validator->set_class($classname);
			$validator->set_value($value);
			$validator->set_message($message);
			$col->add_validator($validator);
		} 
	} 

	/**
	* Set whether or not to use Id generator for primary key.
	* 
	* @param boolean $bit 
	*/
	function set_use_id_generator($bit)
	{
		$this->use_id_generator = $bit;
	} 

	/**
	* Sets the pk information needed to generate a key
	* 
	* @param  $pk_info information needed to generate a key
	*/
	function set_primary_key_method_info($pk_info)
	{
		$this->pk_info = $pk_info;
	} 
	// ---Utility methods for doing intelligent lookup of table names
	/**
	* Tell me if i have PREFIX in my string.
	* 
	* @param data $ A String.
	* @return boolean True if prefix is contained in data.
	* @private 
	*/
	function has_prefix($data)
	{
		return (substr($data, $this->get_prefix()) !== false);
	} 

	/**
	* Removes the PREFIX.
	* 
	* @param string $data A String.
	* @return string A String with data, but with prefix removed.
	* @private 
	*/
	function remove_prefix($data)
	{
		return substr($data, strlen($this->get_prefix()));
	} 

	/**
	* Removes the PREFIX, removes the underscores and makes
	* first letter caps.
	* 
	* SCARAB_FOO_BAR becomes foo_bar.
	* 
	* @param data $ A String.
	* @return string A String with data processed.
	*/
	function remove_under_scores($data)
	{
		$tmp = null;
		$out = '';
		if ($this->has_prefix($data))
		{
			$tmp = $this->remove_prefix($data);
		} 
		else
		{
			$tmp = $data;
		} 

		$tok = strtok($tmp, "_");
		while ($tok)
		{
			$out .= ucfirst($tok);
			$tok = strtok("_");
		} 
		return $out;
	} 

	/**
	* Makes the first letter caps and the rest lowercase.
	* 
	* @param string $data A String.
	* @return string A String with data processed.
	* @private 
	*/
	function first_letter_caps($data)
	{
		return(ucfirst(strtolower($data)));
	} 
} 
