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

require_once(LIMB_DIR . '/core/lib/db/common/result_set_common.class.php');

/**
* mysql implementation of result_set class.
* 
* mysql supports OFFSET / LIMIT natively; this means that no adjustments or checking
* are performed.  We will assume that if the lmit_sql() operation failed that an
* exception was thrown, and that OFFSET/LIMIT will never be emulated for mysql.
*/
class mysql_result_set extends result_set_common
{
	/**
	* 
	* @see result_set::seek()
	*/
	function seek($rownum)
	{ 
		// mysql rows start w/ 0, but this works, because we are
		// looking to move the position _before_ the next desired position
		if (!@mysql_data_seek($this->result, $rownum))
		{
			return false;
		} 
		$this->cursor_pos = $rownum;
		return true;
	} 

	/**
	* 
	* @see result_set::next()
	*/
	function next()
	{
		$this->fields = mysql_fetch_array($this->result, $this->fetchmode);

		if (!$this->fields)
		{
			$errno = mysql_errno($this->conn->get_resource());
			if (!$errno)
			{ 
				// We've advanced beyond end of recordset.
				$this->after_last();
				return false;
			} 
			else
			{
				return new sql_exception(DB_ERROR, "Error fetching result", mysql_error($this->conn->get_resource()));
			} 
		} 

		if (!$this->ignore_assoc_case)
		{
			$this->fields = array_change_key_case($this->fields, CASE_LOWER);
		} 
		// Advance cursor position
		$this->cursor_pos++;
		return true;
	} 

	/**
	* 
	* @see result_set::get_record_count()
	*/
	function get_record_count()
	{
		$rows = @mysql_num_rows($this->result);
		if ($rows === null)
		{
			return new sql_exception(DB_ERROR, "Error fetching num rows", mysql_error($this->conn->get_resource()));
		} 
		return (int) $rows;
	} 

	/**
	* 
	* @see result_set::close()
	*/
	function close()
	{
		@mysql_free_result($this->result);
		$this->fields = array();
	} 

	/**
	* Get string version of column.
	* No rtrim() necessary for mysql, as this happens natively.
	* 
	* @see result_set::get_string()
	*/
	function get_string($column)
	{
		$idx = (is_int($column) ? $column - 1 : $column);
		if (!array_key_exists($idx, $this->fields))
		{
			return new sql_exception(DB_ERROR_INVALID, "Invalid resultset column: " . $column);
		} 
		if ($this->fields[$idx] === null)
		{
			return null;
		} 
		return (string) $this->fields[$idx];
	} 

	/**
	* Returns a unix epoch timestamp based on either a TIMESTAMP or DATETIME field.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1.
	* @return mixed string on success or
	* sql_exception if the column specified is not a valid key in current field array.
	*/
	function get_timestamp($column, $format = 'Y-m-d H:i:s')
	{
		if (is_int($column))
		{
			$column--;
		} // because Java convention is to start at 1
		if (!array_key_exists($column, $this->fields))
		{
			return new sql_exception(DB_ERROR_INVALID, "Invalid resultset column: " . (is_int($column) ? $column + 1 : $column));
		} 

		if ($this->fields[$column] === null)
		{
			return null;
		} 

		$ts = strtotime($this->fields[$column]);
		if ($ts === -1)
		{ 
			// otherwise it's an ugly mysql timestamp!
			// YYYYMMDDHHMMSS
			if (preg_match('/([\d]{4})([\d]{2})([\d]{2})([\d]{2})([\d]{2})([\d]{2})/', $this->fields[$column], $matches))
			{ 
				// YYYY      MM        DD      HH        MM       SS
				// $1    $2      $3    $4    $5     $6
				$ts = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
			} 
		} 
		if ($ts === -1) // if it's still -1, then there's nothing to be done; use a different method.
		{
			return new sql_exception(DB_ERROR_INVALID, "Unable to convert value at column " . (is_int($column) ? $column + 1 : $column) . " to timestamp: " . $this->fields[$column]);
		} 
		if ($ts === null)
		{
			return $ts;
		} 
		if (strpos($format, '%') !== false)
		{
			return strftime($format, $ts);
		} 
		else
		{
			return date($format, $ts);
		} 
	} 
} 
