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

require_once(LIMB_DIR . '/core/lib/db/prepared_statement.class.php');

/**
* Interface for callable statements.
* 
*/
class callable_statement extends prepared_statement
{
	/**
	* Register a parameter as an output param.
	* 
	* @param string $param_index The stored procedure param name (e.g.
	* @val 1).
	* @param int $sql_type The type of the parameter (e.g. Type::BIT) .
	*/
	function register_out_parameter($param_index, $sql_type)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Parameter name (e.g. "@var1").
	* @return array 
	* @throws sql_exception if $param_index was not bound as output variable.
	*/
	function get_array($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Parameter name (e.g. "@var1").
	* @return boolean 
	* @throws sql_exception if $param_index was not bound as output variable.
	*/
	function get_boolean($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Parameter name (e.g. "@var1").
	* @return blob blob object
	* @throws sql_exception if $param_index was not bound as output variable.
	*/
	function get_blob($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Column name (string) or index (int).
	* @return Clob clob object.
	*/
	function get_clob($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Return a formatted date.
	* 
	* The default format for dates returned is preferred (in your locale, as specified using setlocale())
	* format w/o time (i.e. strftime("%x", $val)).  Override this by specifying a format second parameter.  You
	* can also specify a date()-style formatter; if you do, make sure there are no "%" symbols in your format string.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @param string $format Date formatter for use w/ strftime() or date() (it will choose based on examination of format string)
	* @return string Date.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_date($column, $format = '%x')
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Column name (string) or index (int).
	* @return float 
	*/
	function get_float($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Column name (string) or index (int).
	* @return int 
	*/
	function get_int($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param mixed $param_index Column name (string) or index (int).
	* @return string 
	*/
	function get_string($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Return a formatted time.
	* 
	* The default format for times returned is preferred (in your locale, as specified using setlocale())
	* format w/o date (i.e. strftime("%X", $val)).  Override this by specifying a format second parameter.  You
	* can also specify a date()-style formatter; if you do, make sure there are no "%" symbols in your format string.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @param string $format Date formatter for use w/ strftime() or date() (it will choose based on examination of format string)
	* @return string Formatted time.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_time($column, $format = '%X')
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Return a formatted timestamp.
	* 
	* The default format for timestamp is ISO standard YYYY-MM-DD HH:MM:SS (i.e. date('Y-m-d H:i:s', $val).
	* Override this by specifying a format second parameter.  You can also specify a strftime()-style formatter.
	* 
	* Hint: if you want to get the unix timestamp use the "U" formatter string.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @param string $format Date formatter for use w/ strftime() or date() (it will choose based on examination of format string)
	* @return string Timestamp
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_timestamp($column, $format = 'Y-m-d H:i:s')
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 
