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

require_once(LIMB_DIR . '/core/lib/db/result_set.class.php');

/**
* This class implements many shared or common methods needed by resultset drivers.
* 
* A new instance of this class will be returned by the DB implementation
* after processing a query that returns data.
* 
* The get*() methods in this class will format values before returning them. Note
* that if they will return <code>null</code> if the database returned <code>NULL</code>
* which makes these functions easier to use than simply typecasting the values from the
* db. If the requested column does not exist than an exception (sql_exception) will be thrown.
* 
* <code>
* $rs = $conn->execute_query("SELECT MAX(stamp) FROM event", result_set::FETCHMODE_NUM);
* $rs->next();
* 
* $max_stamp = $rs->get_timestamp(1, "d/m/Y H:i:s");
* // $max_stamp will be date string or null if no MAX(stamp) was found
* 
* $max_stamp = $rs->get_timestamp("max(stamp)", "d/m/Y H:i:s");
* // will THROW EXCEPTION, because the resultset was fetched using numeric indexing
* // sql_exception: Invalid resultset column: max(stamp)
* </code>
* 
*/
class result_set_common extends result_set
{
	/**
	* The fetchmode for this recordset.
	* 
	* @var int 
	*/
	var $fetchmode;

	/**
	* DB connection.
	* 
	* @var connection 
	*/
	var $conn;

	/**
	* Resource identifier used for native result set handling.
	* 
	* @var resource 
	*/
	var $result;

	/**
	* The current cursor position (row number). First row is 0.
	* 
	* @var int 
	*/
	var $cursor_pos = 0;

	/**
	* The current unprocessed record/row from the db.
	* 
	* @var array 
	*/
	var $fields;

	/**
	* Whether to convert assoc col case.
	*/
	var $ignore_assoc_case = false;

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function result_set_common(&$conn, &$result, $fetchmode = null)
	{
		if (! is_a($conn, 'connection'))
		{
			debug :: write_warning("parameter 1 not of type 'connection' !",
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 

		$this->conn = &$conn;
		$this->result = &$result;
		if ($fetchmode !== null)
		{
			$this->fetchmode = $fetchmode;
		} 
		else
		{
			$this->fetchmode = result_set::FETCHMODE_ASSOC(); // default
		} 
		$this->ignore_assoc_case = (($conn->get_flags() &db_factory::NO_ASSOC_LOWER()) === db_factory::NO_ASSOC_LOWER());
	} 

	/**
	* 
	* @see result_set::get_iterator()
	*/
	function &get_iterator()
	{
		include_once(LIMB_DIR . '/core/lib/db/result_set_iterator.class.php');
		return new result_set_iterator($this);
	} 

	/**
	* 
	* @see result_set::get_resource()
	*/
	function &get_resource()
	{
		return $this->result;
	} 

	/**
	* 
	* @see result_set::is_ignore_assoc_case()
	*/
	function is_ignore_assoc_case()
	{
		return $this->ignore_assoc_case;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function set_fetchmode($mode)
	{
		$this->fetchmode = $mode;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function get_fetchmode()
	{
		return $this->fetchmode;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function &previous()
	{ 
		// Go back 2 spaces so that we can then advance 1 space.
		$ok = $this->seek($this->cursor_pos - 2);
		if ($ok === false)
		{
			$this->before_first();
			return false;
		} 
		return $this->next();
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function relative($offset)
	{ 
		// which absolute row number are we seeking
		$pos = $this->cursor_pos + ($offset - 1);
		$ok = $this->seek($pos);

		if ($ok === false)
		{
			if ($pos < 0)
			{
				$this->before_first();
			} 
			else
			{
				$this->after_last();
			} 
		} 
		else
		{
			$ok = $this->next();
		} 

		return $ok;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function absolute($pos)
	{
		$ok = $this->seek($pos - 1); // compensate for next() factor
		if ($ok === false)
		{
			if ($pos - 1 < 0)
			{
				$this->before_first();
			} 
			else
			{
				$this->after_last();
			} 
		} 
		else
		{
			$ok = $this->next();
		} 
		return $ok;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function &first()
	{
		if ($this->cursor_pos !== 0)
		{
			$this->seek(0);
		} 
		return $this->next();
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function &last()
	{
		$last = $this->get_record_count();
		if (is_error($last))
		{
			return $last;
		} 

		if ($this->cursor_pos !== ($last = $last - 1))
		{
			$this->seek($last);
		} 

		return $this->next();
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function before_first()
	{
		$this->cursor_pos = 0;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function after_last()
	{
		$rc = $this->get_record_count();
		if (is_error($rc))
		{
			return $rc;
		} 

		$this->cursor_pos = $this->get_record_count() + 1;
		return true;
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function is_after_last()
	{
		return ($this->cursor_pos === $this->get_record_count() + 1);
	} 

	/**
	* 
	* @see result_set::is_before_first()
	*/
	function is_before_first()
	{
		return ($this->cursor_pos === 0);
	} 

	/**
	* 
	* @see result_set::get_cursor_pos()
	*/
	function get_cursor_pos()
	{
		return $this->cursor_pos;
	} 

	/**
	* 
	* @see result_set::get_row()
	*/
	function get_row()
	{
		return $this->fields;
	} 

	/**
	* 
	* @see result_set::get()
	*/
	function get($column)
	{
		$idx = (is_int($column) ? $column - 1 : $column);
		if (!array_key_exists($idx, $this->fields))
		{
			return new sql_exception(DB_ERROR_INVALID, "Invalid resultset column: " . $column);
		} 
		return $this->fields[$idx];
	} 

	/**
	* 
	* @see result_set::get_array()
	*/
	function get_array($column)
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
		return (array) unserialize($this->fields[$idx]);
	} 

	/**
	* 
	* @see result_set::get_boolean()
	*/
	function get_boolean($column)
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
		return (boolean) $this->fields[$idx];
	} 

	/**
	* 
	* @see result_set::get_blob()
	*/
	function get_blob($column)
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
		include_once(LIMB_DIR . '/core/lib/db/util/blob.class.php');
		
		$b = new blob();
		$b->set_contents($this->fields[$idx]);
		return $b;
	} 

	/**
	* 
	* @see result_set::get_clob()
	*/
	function get_clob($column)
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
		include_once(LIMB_DIR . '/core/lib/db/util/clob.class.php');
		$c = new clob();
		$c->set_contents($this->fields[$idx]);
		return $c;
	} 

	/**
	* 
	* @see result_set::get_date()
	*/
	function get_date($column, $format = '%x')
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
		$ts = strtotime($this->fields[$idx]);
		if ($ts === -1)
		{
			return new sql_exception(DB_ERROR_INVALID, "Unable to convert value at column " . $column . " to timestamp: " . $this->fields[$idx]);
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

	/**
	* 
	* @see result_set::get_float()
	*/
	function get_float($column)
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
		return (float) $this->fields[$idx];
	} 

	/**
	* 
	* @see result_set::get_int()
	*/
	function get_int($column)
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
		return (int) $this->fields[$idx];
	} 

	/**
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
		return rtrim((string) $this->fields[$idx]);
	} 

	/**
	* 
	* @see result_set::get_time()
	*/
	function get_time($column, $format = '%X')
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

		$ts = strtotime($this->fields[$idx]);

		if ($ts === -1)
		{
			return new sql_exception(DB_ERROR_INVALID, "Unable to convert value at column " . (is_int($column) ? $column + 1 : $column) . " to timestamp: " . $this->fields[$idx]);
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

	/**
	* 
	* @see result_set::get_timestamp()
	*/
	function get_timestamp($column, $format = 'Y-m-d H:i:s')
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

		$ts = strtotime($this->fields[$idx]);
		if ($ts === -1)
		{
			return new sql_exception(DB_ERROR_INVALID, "Unable to convert value at column " . $column . " to timestamp: " . $this->fields[$idx]);
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

