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

SimpleTestOptions::ignore('test_statement');

class test_statement extends UnitTestCase
{
	/**
	* The database connection.
	* 
	* @var Connection 
	*/
	var $conn;

	function test_statement()
	{
		driver_test_manager::restore();
		
		$this->conn = driver_test_manager::get_connection();
		
		parent :: UnitTestCase();
	}
	
	function test_set_limit()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$stmt = $this->conn->create_statement();
		$stmt->set_limit(10);
		$rs = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$this->assertEqual(10, $rs->get_record_count());
	} 

	function test_set_offset()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$stmt = $this->conn->create_statement();
		$stmt->set_limit(10);
		$stmt->set_offset(5);
		$rs = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());

		$rs->next();

		$this->assertEqual(6, $rs->get_int(1));

		$rs->close(); 
		// test setting offset w/ no limit
		$stmt->set_limit(0);
		$stmt->set_offset(5);
		if(!is_error($stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM())))
		{
			$this->fail("Expected SQLException to be thrown when setting offset w/ no limit.");
		} 
		else
		{
			$this->assertErrorPattern('/Cannot specify an offset without limit/');
		} 
		// try changing the offset info
		$stmt->set_limit(10);
		$stmt->set_offset(4);
		$rs = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());

		$rs->next();

		$this->assertEqual(5, $rs->get_int(1), 0, "Expected new first row to have changed after changing offset.");

		$stmt->close();
	} 

	/**
	* 
	* @todo Implement
	*/
	function test_get_more_results()
	{ 
		// coming sooon..
	} 

	function test_execute_query()
	{
		$exch = driver_test_manager::get_exchange('statement_test.execute_query');
		$stmt = $this->conn->create_statement();
		$rs = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();

		$this->assertEqual(1, $rs->get_int(1));

		$rs->close(); 
		// make sure that getupdatecount returns null
		$this->assertTrue(($stmt->get_update_count() === null), "Expected getUpdateCount() to return NULL since last statement was a query.");
		$stmt->close();
	} 

	function test_execute_update()
	{
		$exch = driver_test_manager::get_exchange('statement_test.execute_update');
		$stmt = $this->conn->create_statement();
		$stmt->execute_update($exch->get_sql());
		$this->assertEqual(1, $stmt->get_update_count());
		$this->assertTrue(($stmt->get_result_set() === null), "Expected getResultSet() to return NULL since last statement was an update.");
		$stmt->close();
	} 

	function test_execute()
	{
		/*$exch = driver_test_manager::get_exchange('statement_test.execute_update');
		$stmt = $this->conn->create_statement();
		$res = $stmt->execute($exch->get_sql());
		$this->assertFalse($res, "Expected resulst of execute() to be FALSE because an update statement was executed (this is to match JDBC return values).");
		$this->assertEqual(1, $stmt->get_update_count());

		$exch = driver_test_manager::get_exchange('statement_test.execute_query');
		$stmt = $this->conn->create_statement();
		$res = $stmt->execute($exch->get_sql());
		$this->assertTrue($res, "Expected resulst of execute() to be TRUE because a select query was executed (this is to match JDBC return values).");
		$this->assertIsA($stmt->get_result_set(), 'result_set', "Expected to be able to getResultSet() after call to execute() w/ SELECT query.");

		$stmt->close();*/
	} 
} 
