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

require_once(LIMB_DIR . '/core/lib/db/db_types.class.php');

/**
* mysql types / type map.
* 
*/
class mysql_types extends db_types
{
	/**
	* * Map mysql native types to db (JDBC) types.
	*/
	var $type_map = null;
	/**
	* * Reverse mapping, created on demand.
	*/
	var $reverse_map = null;

	/**
	* This method returns the generic db (JDBC-like) type
	* when given the native db type.
	* 
	* @param string $native_type DB native type (e.g. 'TEXT', 'byetea', etc.).
	* @return int db native type (e.g. db_types::LONGVARCHAR, db_types::BINARY, etc.).
	*/
	function get_type($native_type)
	{
		$self = &mysql_types::instance();

		$t = strtolower($native_type);
		if (isset($self->type_map[$t]))
		{
			return $self->type_map[$t];
		} 
		else
		{
			return db_types::OTHER();
		} 
	} 

	/**
	* This method will return a native type that corresponds to the specified
	* db (JDBC-like) type.
	* If there is more than one matching native type, then the LAST defined
	* native type will be returned.
	* 
	* @param int $db_type 
	* @return string Native type string.
	*/
	function get_native_type($db_type)
	{
		$self = &mysql_types::get_instance();

		if ($self->reverse_map === null)
		{
			$self->reverse_map = array_flip($self->type_map);
		} 

		return @$self->reverse_map[$db_type];
	} 

	/*
  * @private
  */
	function &instance()
	{
		static $instance;

		if ($instance === null)
		{
			$instance = new mysql_types();
			$instance->type_map = array
			('tinyint' => db_types::TINYINT(),
				'smallint' => db_types::SMALLINT(),
				'mediumint' => db_types::SMALLINT(),
				'int' => db_types::INTEGER(),
				'integer' => db_types::INTEGER(),
				'bigint' => db_types::BIGINT(),
				'int24' => db_types::BIGINT(),
				'real' => db_types::REAL(),
				'float' => db_types::FLOAT(),
				'decimal' => db_types::DECIMAL(),
				'numeric' => db_types::NUMERIC(),
				'double' => db_types::DOUBLE(),
				'char' => db_types::CHAR(),
				'varchar' => db_types::VARCHAR(),
				'date' => db_types::DATE(),
				'time' => db_types::TIME(),
				'year' => db_types::YEAR(),
				'datetime' => db_types::TIMESTAMP(),
				'timestamp' => db_types::TIMESTAMP(),
				'tinyblob' => db_types::BINARY(),
				'blob' => db_types::VARBINARY(),
				'mediumblob' => db_types::VARBINARY(),
				'longblob' => db_types::VARBINARY(),
				'tinytext' => db_types::VARCHAR(),
				'mediumtext' => db_types::LONGVARCHAR(),
				'text' => db_types::LONGVARCHAR(),
				'enum' => db_types::CHAR(),
				'set' => db_types::CHAR()
				);
		} 

		return $instance;
	} 
} 
