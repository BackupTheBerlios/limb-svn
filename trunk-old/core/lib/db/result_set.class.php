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
* This is the interface for classes the wrap db results.
* 
* The get*() methods in this interface will format values before returning them. Note
* that if they will return null if the database returned NULL.  If the requested column does
* not exist than an exception (sql_exception) will be thrown.
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
* This class implements SPL iterator_aggregate, so you may iterate over the database results
* using foreach():
* <code>
* foreach($rs as $row) {
*    print_r($row); // row is assoc array returned by get_row()
* }
* </code>
* 
*/
class result_set
{
	/**
	* Index result set by field name.
	*/
	function FETCHMODE_ASSOC()
	{
		return (1);
	} 

	/**
	* Index result set numerically.
	*/
	function FETCHMODE_NUM()
	{
		return (2);
	} 

	/**
	* Get the PHP native resource for the result.
	* Arguably this should not be part of the interface: i.e. every driver should implement
	* it if they have a result resource, but conceivably drivers could be created that do
	* not.  For now every single driver does have a "dblink" resource property, and other
	* classes (e.g. result_set) need this info in order to get correct native errors.  We'll
	* leave it in for now, as it helps with driver development, with the caveat that it
	* could be removed from the interface at a later point.
	* 
	* @return resource Query result or NULL if not not applicable.
	*/
	function get_resource()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Sets the fetchmode used to retrieve results.
	* Changing fetchmodes mid-result retrieval is supported (haven't encountered any drivers
	* that don't support that yet).
	* 
	* @param int $mode result_set::FETCHMODE_NUM or  result_set::FETCHMODE_ASSOC (default).
	* @return void 
	*/
	function set_fetchmode($mode)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets the fetchmode used to retrieve results.
	* 
	* @return int result_set::FETCHMODE_NUM or result_set::FETCHMODE_ASSOC (default).
	*/
	function get_fetchmode()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Whether assoc result keys get left alone -- as opposed to converted to lowercase.
	* If the case change stuff goes back to being more complicated (allowing conver to upper,
	* e.g.) then we'll add new methods but this method will always indicate whether any
	* case conversions should be (or have been) performed at all.
	* This defaults to true unless db_factory::NO_ASSOC_LOWER() flag has been passed to connection.
	* This property is read-only since it must be set when connection is created.  The
	* reason for this behavior is some drivers (e.g. SQLite) do the case conversions internally
	* based on a PHP ini value; it would not be possible to change the behavior from the result_set
	* (since query has already been executed).
	* 
	* @return boolean 
	*/
	function is_ignore_assoc_case()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Moves the internal cursor to the next position and fetches the row at that position.
	* 
	* @return boolean <tt>true</tt> if success, <tt>false</tt> if no next record or
	* sql_exception on any driver-level errors.
	*/
	function next()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Moves the internal cursor to the previous position and fetches the
	* row at that position.
	* 
	* @return boolean <tt>true</tt> if success, <tt>false</tt> if no previous record.
	* @throws sql_exception - if unable to move to previous position
	*                       - if result_set doesn't support reverse scrolling
	*/
	function previous()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Moves the cursor a relative number of rows, either positive or negative and fetches
	* the row at that position.
	* 
	* Attempting to move beyond the first/last row in the result set positions the cursor before/after
	* the first/last row and issues a Warning. Calling relative(0) is valid, but does not change the cursor
	* position.
	* 
	* @param integer $offset 
	* @return boolean <tt>true</tt> if cursor is on a row, <tt>false</tt> otherwise.
	* @throws sql_exception - if unable to move to relative position
	*                       - if rel pos is negative & result_set doesn't support reverse scrolling
	*/
	function relative($offset)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Moves the cursor to an absolute cursor position and fetches the row at that position.
	* 
	* Attempting to move beyond the first/last row in the result set positions the cursor before/after
	* the first/last row and issues a Warning.
	* 
	* @param integer $pos cursor position, first position is 1.
	* @return boolean <tt>true</tt> if cursor is on a row, <tt>false</tt> otherwise.
	* @throws sql_exception - if unable to move to absolute position
	*                       - if position is before current pos & result_set doesn't support reverse scrolling
	*/
	function absolute($pos)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Moves cursor position WITHOUT FETCHING ROW AT THAT POSITION.
	* 
	* Generally this method is for internal driver stuff (e.g. other methods like
	* absolute() or relative() might call this and then call next() to get the row).
	* This method is public to facilitate more advanced result_set scrolling tools
	* -- e.g. cleaner implimentation of result_set_iterator.
	* 
	* Some drivers will emulate seek() and not allow reverse seek (Oracle).
	* 
	* Seek is 0-based, but seek() is only for moving to the space _before_ the record
	* that you want to read.  I.e. if you seek(0) and then call next() you will have the
	* first row (i.e. same as calling first() or absolute(1)).
	* 
	* <strong>IMPORTANT:  You cannot rely on the return value of this method to know whether a given
	* record exists for reading.  In some cases seek() will correctly return <code>false</code> if
	* the position doesn't exist, but in other drivers the seek is not performed until the
	* record is fetched. You can check the return value of absolute() if you need to know
	* whether a specific rec position is valid.</strong>
	* 
	* @param int $rownum The cursor pos to seek to.
	* @return boolean true on success, false if unable to seek to specified record.
	* @throws sql_exception if trying to seek backwards with a driver that doesn't
	*                       support reverse-scrolling
	*/
	function seek($rownum)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Move cursor to beginning of recordset.
	* 
	* @return boolean <tt>true</tt> on success or <tt>false</tt> if not found.
	* @throws sql_exception - if unable to move to first position
	*                       - if not at first pos & result_set doesn't support reverse scrolling
	*/
	function first()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Move cursor to end of recordset.
	* 
	* @return boolean <tt>true</tt> on success or <tt>false</tt> if not found.
	* @return sql_exception - if unable to move to last position
	*                       - if unable to get num rows
	*/
	function last()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Sets cursort to before first record. This does not actually seek(), but
	* simply sets cursor pos to 0.
	* This is useful for inserting a record before the first in the set, etc.
	* 
	* @return void 
	*/
	function before_first()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Sets cursort to after the last record. This does not actually seek(), but
	* simply sets the cursor pos  to last + 1.
	* This [will be] useful for inserting a record after the last in the set,
	* when/if db supports updateable result_sets.
	* 
	* @return TRUE on success, sql_exception on error
	*/
	function after_last()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Checks whether cursor is after the last record.
	* 
	* @return boolean 
	* @throws sql_exception on any driver-level error.
	*/
	function is_after_last()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Checks whether cursor is before the first record.
	* 
	* @return boolean 
	* @throws sql_exception on any driver-level error.
	*/
	function is_before_first()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns the current cursor position.
	* Cursor positions start at 0, but as soon as first row is fetched
	* cursor position is 1. (so first row is 1)
	* 
	* @return int 
	*/
	function get_cursor_pos()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets current fields (assoc array).
	* 
	* @return array 
	*/
	function get_row()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get the number of rows in a result set.
	* 
	* @return int the number of rows
	* @throws sql_exception - if unable to get a rowcount.
	*/
	function get_record_count()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Frees the resources allocated for this result set.
	* Also empties any internal field array so that any calls to
	* get() method on closed result_set will result in "Invalid column" sql_exception.
	* 
	* @return void 
	*/
	function close()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* A generic get method returns unformatted (=string) value.
	* This returns the raw results from the database.  Usually this will be a string, but some drivers
	* also can return objects (lob descriptors, etc) in certain cases.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used) (if result_set::FETCHMODE_NUM was used).
	* @return mixed Usually expect a string.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get($column)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Reads a column as an array.
	* The value of the column is unserialized & returned as an array.  The generic case of this function is
	* very PHP-specific.  Other drivers (e.g. Postgres) will format values into their native array format.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return array value or null if database returned null.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_array($column)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns value translated to boolean.
	* Default is to map 0 => false, 1 => true, but some database drivers may override this behavior.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return boolean value or null if database returned null.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_boolean($column)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns Blob with contents of column value.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return Blob New Blob with data from column or null if database returned null.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_blob($column)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns Clob with contents of column value.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return Clob New Clob object with data from column or null if database returned null.
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_clob($column)
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
	* @return string formatted date or null if database returned null
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_date($column, $format = '%x')
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns value cast as a float (in PHP this is same as double).
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return float value or null if database returned null
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_float($column)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns value cast as integer.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return int value or null if database returned null
	* @see get_integer
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_int($column)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns value cast as string.
	* 
	* @param mixed $column Column name (string) or index (int) starting with 1 (if result_set::FETCHMODE_NUM was used).
	* @return string value or null if database returned null
	* @see get
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_string($column)
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
	* @return string Formatted time or null if database returned null
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
	* @return string Formatted timestamp or null if database returned null
	* @throws sql_exception - If the column specified is not a valid key in current field array.
	*/
	function get_timestamp($column, $format = 'Y-m-d H:i:s')
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 

