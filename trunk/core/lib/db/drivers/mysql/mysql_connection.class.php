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

require_once(LIMB_DIR . '/core/lib/db/common/connection_common.class.php');
require_once(LIMB_DIR . '/core/lib/db/drivers/mysql/mysql_result_set.class.php');

/**
* mysql implementation of connection.
* 
*/
class mysql_connection extends connection_common
{
	/**
	* * Current database (used in mysql_select_db()).
	*/
	var $database;

	/**
	* Connect to a database and log in as the specified user.
	* 
	* @param  $dsn the data source name (see db_factory::parse_dsn for syntax)
	* @param  $flags Any conneciton flags.
	* @access public 
	* @return TRUE on success, sql_exception on error
	*/
	function connect($dsninfo, $flags = 0)
	{
		if (! extension_loaded('mysql'))
		{
			return new sql_exception(DB_ERROR_EXTENSION_NOT_FOUND, 'mysql extension not loaded');
		} 

		$this->dsn = $dsninfo;
		$this->flags = $flags;

		$persistent = ($flags & db_factory::PERSISTENT()) === db_factory::PERSISTENT();

		if (isset($dsninfo['protocol']) && $dsninfo['protocol'] == 'unix')
		{
			$dbhost = ':' . $dsninfo['socket'];
		} 
		else
		{
			$dbhost = $dsninfo['hostspec'] ? $dsninfo['hostspec'] : 'localhost';
			if (!empty($dsninfo['port']))
			{
				$dbhost .= ':' . $dsninfo['port'];
			} 
		} 

		$user = $dsninfo['username'];
		$pw = $dsninfo['password'];

		$connect_function = $persistent ? 'mysql_pconnect' : 'mysql_connect';

		@ini_set('track_errors', true);
		if ($dbhost && $user && $pw)
		{
			$conn = @$connect_function($dbhost, $user, $pw);
		} 
		elseif ($dbhost && $user)
		{
			$conn = @$connect_function($dbhost, $user);
		} 
		elseif ($dbhost)
		{
			$conn = @$connect_function($dbhost);
		} 
		else
		{
			$conn = false;
		} 

		@ini_restore('track_errors');
		if (empty($conn))
		{
			if (($err = @mysql_error()) != '')
			{
				return new sql_exception(DB_ERROR_CONNECT_FAILED, "connect failed", $err);
			} 
			elseif (empty($php_errormsg))
			{
				return new sql_exception(DB_ERROR_CONNECT_FAILED, "connect failed");
			} 
			else
			{
				return new sql_exception(DB_ERROR_CONNECT_FAILED, "connect failed", $php_errormsg);
			} 
		} 
		
		$this->dblink = $conn;
		
		if ($dsninfo['database'])
		{
			if (($e = $this->select_database($dsninfo['database'])) !== true)
			{
				return $e;
			} 
		} 
		return true;
	} 

	/**
	* 
	* @see connection::get_db_info()
	*/
	function &get_db_info()
	{
		include_once(LIMB_DIR . '/core/lib/db/drivers/mysql/metadata/mysql_db_info.class.php');
		return new mysql_db_info($this);
	} 

	/**
	* 
	* @see connection::get_id_generator()
	*/
	function &get_id_generator()
	{
		include_once(LIMB_DIR . '/core/lib/db/drivers/mysql/mysql_id_generator.class.php');
		return new mysql_id_generator($this);
	} 

	/**
	* 
	* @see connection::prepare_statement()
	*/
	function &prepare_statement(&$sql)
	{
		include_once(LIMB_DIR . '/core/lib/db/drivers/mysql/mysql_prepared_statement.class.php');
		return new mysql_prepared_statement($this, $sql);
	} 

	/**
	* 
	* @see connection::prepare_call()
	*/
	function &prepare_call(&$sql)
	{
		return new sql_exception(DB_ERROR_UNSUPPORTED, 'MySQL does not support stored procedures.');
	} 

	/**
	* 
	* @see connection::create_statement()
	*/
	function &create_statement()
	{
		include_once(LIMB_DIR . '/core/lib/db/drivers/mysql/mysql_statement.class.php');
		return new mysql_statement($this);
	} 

	/**
	* 
	* @see connection::disconnect()
	*/
	function close()
	{
		$ret = mysql_close($this->dblink);
		$this->dblink = null;
		return $ret;
	} 

	/**
	* 
	* @see connection::execute_query()
	*/
	function &execute_query($sql, $fetchmode = null)
	{
		$this->last_query = $sql;

		$result = @mysql_query($sql, $this->dblink);

		if ($result === false)
		{
			return new sql_exception(DB_ERROR, 'Could not execute query', mysql_error($this->dblink), $sql);
		} 

		return new mysql_result_set($this, $result, $fetchmode);
	} 

	/**
	* 
	* @see connection::execute_update()
	*/
	function &execute_update($sql)
	{
		$this->last_query = $sql;

		if (!$this->autocommit)
		{
			if ($this->transaction_opcount == 0)
			{
				$result = @mysql_query('SET AUTOCOMMIT=0', $this->dblink);
				$result = @mysql_query('BEGIN', $this->dblink);
				if ($result === false)
				{
					return new sql_exception(DB_ERROR, 'Could not begin transaction', mysql_error($this->dblink));
				} 
			} 

			$this->transaction_opcount++;
		} 

		$result = @mysql_query($sql, $this->dblink);

		if ($result === false)
		{
			return new sql_exception(DB_ERROR, 'Could not execute update', mysql_error($this->dblink), $sql);
		} 

		return (int) mysql_affected_rows($this->dblink);
	} 

	/**
	* Commit the current transaction.
	* 
	* @return TRUE on success, sql_exception on error.
	* @see connection::commit()
	*/
	function commit()
	{
		if ($this->transaction_opcount > 0)
		{
			$result = @mysql_query('COMMIT', $this->dblink);
			$result = @mysql_query('SET AUTOCOMMIT=1', $this->dblink);
			$this->transaction_opcount = 0;

			if ($result === false)
			{
				return new sql_exception(DB_ERROR, 'Can not commit transaction', mysql_error($this->dblink));
			} 
		} 

		return true;
	} 

	/**
	* Roll back (undo) the current transaction.
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function rollback()
	{
		if ($this->transaction_opcount > 0)
		{
			$result = @mysql_query('ROLLBACK', $this->dblink);
			$result = @mysql_query('SET AUTOCOMMIT=1', $this->dblink);
			$this->transaction_opcount = 0;

			if ($result === false)
			{
				return new sql_exception(DB_ERROR, 'Could not rollback transaction', mysql_error($this->dblink));
			} 
		} 

		return true;
	} 

	/**
	* Gets the number of rows affected by the data manipulation
	* query.
	* 
	* @return int Number of rows affected by the last query.
	*/
	function get_update_count()
	{
		return (int) @mysql_affected_rows($this->dblink);
	} 
	
	function select_database($database)
	{
		if($database == $this->database)
			return true;
		
		$dblink = $this->dblink;
		
		if (! @mysql_select_db($database, $this->dblink))
		{
			switch (mysql_errno($this->dblink))
			{
				case 1049:
					$e = new sql_exception(DB_ERROR_NOSUCHDB, "no such database", mysql_error($this->dblink));
					break;
				case 1044:
					$e = new sql_exception(DB_ERROR_ACCESS_VIOLATION, "access violation", mysql_error($this->dblink));
					break;
				default:
					$e = new sql_exception(DB_ERROR, "cannot select database", mysql_error($this->dblink));
			} 
			return $e;
		}
		
		$this->database = $database;
		
		return true;
	}
} 
