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
* Generic db types modeled on JDBC types.
*/
class db_types
{
  function BOOLEAN()       { return (1); }
  function BIGINT()        { return (2); }
  function SMALLINT()      { return (3); }
  function TINYINT()       { return (4); }
  function INTEGER()       { return (5); }
  function CHAR()          { return (6); }
  function VARCHAR()       { return (7); }
  function TEXT()          { return (17); }
  function FLOAT()         { return (8); }
  function DOUBLE()        { return (9); }
  function DATE()          { return (10); }
  function TIME()          { return (11); }
  function TIMESTAMP()     { return (12); }
  function VARBINARY()     { return (13); }
  function NUMERIC()       { return (14); }
  function BLOB()          { return (15); }
  function CLOB()          { return (16); }
  function LONGVARCHAR()   { return (17); }
  function DECIMAL()       { return (18); }
  function REAL()          { return (19); }
  function BINARY()        { return (20); }
  function LONGVARBINARY() { return (21); }
  function YEAR()          { return (22); }
  /** this is "ARRAY" from JDBC types */
  function ARR()           { return (23); }
  function OTHER()         { return (-1); }


	/**
	* * Map of db type integers to the setter/getter affix.
	*/
	var $affix_map = null;
	/**
	* * Map of db type integers to their textual name.
	*/
	var $db_type_map = null;

	/**
	* This method returns the generic db (JDBC-like) type
	* when given the native db type.
	* 
	* @param string $native_type DB native type (e.g. 'TEXT', 'byetea', etc.).
	* @return int db native type (e.g. Types::LONGVARCHAR, Types::BINARY, etc.).
	*/
	function get_type($native_type)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* This method will return a native type that corresponds to the specified
	* db (JDBC-like) type.
	* If there is more than one matching native type, then the LAST defined
	* native type will be returned.
	* 
	* @return string Native type string.
	*/
	function get_native_type($db_type)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets the "affix" to use for result_set::get*() and prepared_statement::set*() methods.
	* <code>
	* $setter = 'set' . db_types::get_affix(db_types::INTEGER);
	* $stmt->$setter(1, $intval);
	* // or
	* $getter = 'get' . db_types::get_affix(db_types::TIMESTAMP);
	* $timestamp = $rs->$getter();
	* </code>
	* 
	* @param int $db_type The db types.
	* @return mixed The default affix for getting/setting cols of this type on success,
	* sql_exception if $db_type does not correspond to an affix
	*/
	function get_affix($db_type)
	{
		$self = &db_types::instance();

		if (! isset($self->affix_map[$db_type]))
		{
			return new sql_exception(DB_ERROR, "Unable to return 'affix' for unknown dbType: " . $db_type);
		} 
		return $self->affix_map[$db_type];
	} 

	/**
	* Given a PHP variable, returns the correct affix (for getter/setter) to use based
	* on the PHP type of the variable.
	* 
	* @param mixed $ The PHP value for which to get affix.
	* @return string 
	*/
	function get_affix_for_value($value)
	{
	} 

	/**
	* Given the integer type, this method will return the corresponding type name.
	* 
	* @param int $db_type the integer db type.
	* @return string The name of the db type (e.g. 'VARCHAR').
	*/
	function getdb_name($db_type)
	{
		$self = &db_types::instance();

		if (! isset($self->db_type_map[$db_type]))
		{
			return null;
		} 
		return $self->db_type_map[$db_type];
	} 

	/**
	* Given the name of a type (e.g. 'VARCHAR') this method will return the corresponding integer.
	* 
	* @param string $db_type_name The case-sensisive (must be uppercase) name of the db type (e.g. 'VARCHAR').
	* @return int the db type.
	*/
	function getdb_code($db_type_name)
	{
		$self = &db_types::instance();
		$type = array_search($db_type_name, $self->db_type_map);

		if ($type === false)
		{
			return null;
		} 

		return $type;
	} 

	/*
  * @private
  */
	function &instance()
	{
		static $instance;

		if ($instance === null)
		{
			$instance = new db_types();
			$instance->affix_map = array
			(
				db_types::BOOLEAN() => 'boolean',
				db_types::BIGINT() => 'int',
				db_types::CHAR() => 'string',
				db_types::DATE() => 'date',
				db_types::DOUBLE() => 'float',
				db_types::FLOAT() => 'float',
				db_types::INTEGER() => 'int',
				db_types::SMALLINT() => 'int',
				db_types::TINYINT() => 'int',
				db_types::TIME() => 'time',
				db_types::TIMESTAMP() => 'timestamp',
				db_types::VARCHAR() => 'string',
				db_types::VARBINARY() => 'blob',
				db_types::NUMERIC() => 'float',
				db_types::BLOB() => 'blob',
				db_types::CLOB() => 'clob',
				db_types::LONGVARCHAR() => 'string',
				db_types::DECIMAL() => 'float',
				db_types::REAL() => 'float',
				db_types::BINARY() => 'blob',
				db_types::LONGVARBINARY() => 'blob',
				db_types::YEAR() => 'int',
				db_types::ARR() => 'array',
				db_types::OTHER() => '', // get() and set() for unknown
				);

			$instance->db_type_map = array
			(
				db_types::BOOLEAN() => 'BOOLEAN',
				db_types::BIGINT() => 'BIGINT',
				db_types::SMALLINT() => 'SMALLINT',
				db_types::TINYINT() => 'TINYINT',
				db_types::INTEGER() => 'INTEGER',
				db_types::NUMERIC() => 'NUMERIC',
				db_types::DECIMAL() => 'DECIMAL',
				db_types::REAL() => 'REAL',
				db_types::FLOAT() => 'FLOAT',
				db_types::DOUBLE() => 'DOUBLE',
				db_types::CHAR() => 'CHAR',
				db_types::VARCHAR() => 'VARCHAR',
				db_types::TEXT() => 'TEXT',
				db_types::TIME() => 'TIME',
				db_types::TIMESTAMP() => 'TIMESTAMP',
				db_types::DATE() => 'DATE',
				db_types::YEAR() => 'YEAR',
				db_types::VARBINARY() => 'VARBINARY',
				db_types::BLOB() => 'BLOB',
				db_types::CLOB() => 'CLOB',
				db_types::LONGVARCHAR() => 'LONGVARCHAR',
				db_types::BINARY() => 'BINARY',
				db_types::LONGVARBINARY() => 'LONGVARBINARY',
				db_types::ARR() => 'ARR',
				db_types::OTHER() => 'OTHER', // string is "raw" return
				);
		} 

		return $instance;
	} 
} 
