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

SimpleTestOptions::ignore('test_connection');

class test_connection extends UnitTestCase
{
	var $conn;

	function setUp()
	{ 
		driver_test_manager::restore();
		
		$this->conn = driver_test_manager::get_connection();
	} 

	function tearDown()
	{ 
	} 

	/**
	* Test update count for insert, update, and delete.
	*/
	function test_get_update_count()
	{
		$exc = driver_test_manager::get_exchange('connection_test.get_update_count.UPDATE');
		$count = $this->conn->execute_update($exc->get_sql());
		$rs = $this->conn->execute_query("SELECT * FROM products WHERE ProductID = 2");
		$this->assertEqual((int) $exc->get_result(), $count);

		$exc = driver_test_manager::get_exchange('connection_test.get_update_count.DELETE');
		$count = $this->conn->execute_update($exc->get_sql());
		$this->assertEqual((int) $exc->get_result(), $count);

		$exc = driver_test_manager::get_exchange('connection_test.get_update_count.INSERT');
		$count = $this->conn->execute_update($exc->get_sql());
		$this->assertEqual((int) $exc->get_result(), $count);
	} 

	/**
	* Test for correct behavior in turning on or off auto-commit.
	* This function also tests recordset::get_assertTrue(), as a side-effect.
	*/
	function test_set_auto_commit()
	{ 
		// by default auto-commit is TRUE.
		$exch = driver_test_manager::get_exchange('record_count');
		$count_sql = $exch->get_sql();
		$rs = $this->conn->execute_query($count_sql, result_set::FETCHMODE_NUM());
		$rs->next();
		$total = $rs->get_int(1);
		$this->assertEqual((int) $exch->get_result(), $total); 
		// now begin a transaction
		$this->conn->set_auto_commit(false);
		$this->assertFalse($this->conn->get_auto_commit(), "get_auto_commit() did not return FALSE after just having set it to false.");

		$exch = driver_test_manager::get_exchange('connection_test.set_auto_commit.DELTRAN1');
		$deleted1 = $this->conn->execute_update($exch->get_sql());

		$exch = driver_test_manager::get_exchange('connection_test.set_auto_commit.DELTRAN2');
		$deleted2 = $this->conn->execute_update($exch->get_sql());

		$total_should_be = $total - ($deleted1 + $deleted2);

		$this->conn->set_auto_commit(true); // will implicitly commit the transaction
		$this->assertErrorPattern("/Changing autocommit in mid-transaction;.*/"); 
		// compare the actual total w/ what we expect
		$rs = $this->conn->execute_query($count_sql, result_set::FETCHMODE_NUM());
		$rs->next();
		$new_actual_total = $rs->get_int(1);

		$this->assertEqual($total_should_be, $new_actual_total, "Failed to find correct num of records after implicit transaction commit using set_auto_commit(TRUE).");
	} 
	/**
	* Tests explicit commit function.
	*/
	function test_commit()
	{ 
		// by default auto-commit is TRUE.
		$exch = driver_test_manager::get_exchange('record_count');
		$count_sql = $exch->get_sql();
		$rs = $this->conn->execute_query($count_sql, result_set::FETCHMODE_NUM());
		$rs->next();
		$total = $rs->get_int(1); 
		// now begin a transaction
		$this->conn->set_auto_commit(false);

		$exch = driver_test_manager::get_exchange('connection_test.set_auto_commit.DELTRAN1');
		$deleted1 = $this->conn->execute_update($exch->get_sql());

		$exch = driver_test_manager::get_exchange('connection_test.set_auto_commit.DELTRAN2');
		$deleted2 = $this->conn->execute_update($exch->get_sql());

		$total_should_be = $total - ($deleted1 + $deleted2);

		$this->conn->commit(); 
		// compare the actual total w/ what we expect
		$rs = $this->conn->execute_query($count_sql, result_set::FETCHMODE_NUM());
		$rs->next();
		$new_actual_total = $rs->get_int(1);

		$this->assertEqual($total_should_be, $new_actual_total, "Failed to find correct num of records after explicit transaction commit.");

		$this->conn->set_auto_commit(true);
	} 
	
	function test_rollback()
	{
		$exch = driver_test_manager::get_exchange('record_count');
		$count_sql = $exch->get_sql();
		$rs = $this->conn->execute_query($count_sql, result_set::FETCHMODE_NUM());
		$rs->next();
		$total = $rs->get_int(1); 
		
		$this->conn->set_auto_commit(false); 
		// not sure exactly how to test this yet ...
		$exch = driver_test_manager::get_exchange('connection_test.set_auto_commit.DELTRAN1');
		$deleted1 = $this->conn->execute_update($exch->get_sql());

		$exch = driver_test_manager::get_exchange('connection_test.set_auto_commit.DELTRAN2');
		$deleted2 = $this->conn->execute_update($exch->get_sql());

		$this->conn->rollback(); 
		// compare the actual total w/ what we expect
		$rs = $this->conn->execute_query($count_sql, result_set::FETCHMODE_NUM());
		$rs->next();
		$new_actual_total = $rs->get_int(1);

		$this->assertEqual($total, $new_actual_total, "Failed to find correct (same) num of records in table after rollback().");

		$this->conn->set_auto_commit(true);
	} 
	
	/**
	* Test the apply_limit() method.  By default this method will not modify the values provided.
	* Subclasses must override this method to test for appropriate SQL modifications.
	*/
	function test_apply_limit()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

	} 
} 
