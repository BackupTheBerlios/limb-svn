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
* <p>
* This can be used to handle cases where transaction support is optional.
* The second parameter of beginOptionaltransaction will determine with a transaction
* is used or not. If a transaction is not used, the commit and rollback methods
* do not have any effect. Instead it simply makes the logic easier to follow
* by cutting down on the if statements based solely on whether a transaction
* is needed or not.
* 
*/
class transaction
{
	/**
	* Begin a transaction.  This method will fallback gracefully to
	* return a normal connection, if the database being accessed does
	* not support transactions.
	* 
	* @param string $connectioname Name of database.
	* @return connection The connection for the transaction.
	* @throws db_factoryexception
	*/
	function &begin($db_name)
	{
		$con = &db_factory::get_connection($db_name);
		if (($e = $con->set_auto_commit(false)) !== true)
		{
			return new propel_exception(LIMB_ERROR_DB, $e);
		} 
		return $con;
	} 

	/**
	* Begin a transaction.  This method will fallback gracefully to
	* return a normal connection, if the database being accessed does
	* not support transactions.
	* 
	* @param sring $connectioname Name of database.
	* @param boolean $usetransaction If false, a transaction won't be used.
	* @return connection The connection for the transaction.
	* @throws db_factoryexception
	*/
	function &begin_optional($db_name, $use_transaction)
	{
		$con = &db_factory::get_connection($db_name);
		if ($use_transaction)
		{
			$e = $con->set_auto_commit(false);
			if (is_error($e))
			{
				return new propel_exception(LIMB_ERROR_DB, $e);
			} 
		} 
		return $con;
	} 

	/**
	* Commit a transaction.  This method takes care of releasing the
	* connection after the commit.  In databases that do not support
	* transactions, it only returns the connection.
	* 
	* @param connection $con The connection for the transaction.
	* @return void 
	* @throws db_factoryexception
	*/
	function commit(&$con)
	{
		if ($con === null)
		{
			return new exception(LIMB_ERROR,
				"connection object was null. "
				 . "This could be due to a misconfiguration. "
				 . "Check the logs and db_factory properties "
				 . "to better determine the cause.");
		} 
		if ($con->get_auto_commit() === false)
		{
			if (($e = $con->commit()) !== true)
			{
				return new propel_exception(LIMB_ERROR_DB, $e);
			} 
			$con->set_auto_commit(true);
		} 
	} 

	/**
	* Roll back a transaction in databases that support transactions.
	* It also releases the connection. In databases that do not support
	* transactions, this method will log the attempt and release the
	* connection.
	* 
	* @param connection $con The connection for the transaction.
	* @return void 
	* @throws db_factoryexception
	*/
	function rollback(&$con)
	{
		if ($con === null)
		{
			return new exception(LIMB_ERROR,
				"connection object was null. "
				 . "This could be due to a misconfiguration. "
				 . "Check the logs and db_factory properties "
				 . "to better determine the cause.");
		} 

		if ($con->get_auto_commit() === false)
		{
			if (($e = $con->rollback()) !== true)
			{
				/*$logger->err("An attempt was made to rollback a transaction "
					 . "but the database did not allow the operation to be "
					 . "rolled back: " . $e->get_message());*/
				return new propel_exception(LIMB_ERROR_DB, $e);
			} 
			/*[MA]: should this be checked as well ?*/
			$con->set_auto_commit(true);
		} 
	} 

	/**
	* Roll back a transaction without throwing errors if they occur.
	* 
	* @param connection $con The connection for the transaction.
	* @return void 
	*/
	function safe_rollback(&$con)
	{
		if (is_error($e = transaction::rollback($con)))
		{
			/*$logger = &db_factory::logger();
			$logger->err("An error occured during rollback: " . $e->get_message());*/
		} 
	} 
} 