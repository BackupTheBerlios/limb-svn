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
require_once(LIMB_DIR . '/core/lib/db/util/blob.class.php');
require_once(LIMB_DIR . '/core/lib/db/util/clob.class.php');

SimpleTestOptions::ignore('test_prepared_statement');

class test_prepared_statement extends UnitTestCase
{
	var $conn;
	
	function test_prepared_statement()
	{
		parent :: UnitTestCase();
		
		$this->conn = driver_test_manager::get_connection();
	}
	
	function setUp()
	{
		driver_test_manager::restore();
		
		$this->conn = driver_test_manager::get_connection();
	} 

	/**
	* Supports get_blob() and set_blob() tests.
	* 
	* @see result_set_test::get_blob()
	* @see prepared_statement_test::set_blob()
	* @return blob 
	*/
	function create_blob()
	{ 
		// read in the file
		$b = new blob();
		$b->set_input_file(LIMB_DIR . '/tests/cases/db/etc/lob/bit.png');
		return $b;
	} 

	/**
	* Supports get_clob() and set_clob() tests.
	* 
	* @see result_set_test::get_clob()
	* @see prepared_statement_test::set_clob()
	* @return clob 
	*/
	function create_clob()
	{ 
		// read in the file
		$c = new clob();
		$c->set_input_file(LIMB_DIR . '/tests/cases/db/etc/lob/big.txt');
		return $c;
	} 

	/**
	* Set blob value.
	* 
	* @param blob $blob The blob to insert into database.
	*/
	function set_blob($blob)
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_blob');
		$this->conn->set_auto_commit(false);
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1); // pkey
		$stmt->set_string(2, "TestName");
		$stmt->set_blob(3, $blob);
		$stmt->execute_update();
		$this->conn->commit();
		$this->conn->set_auto_commit(true);
	} 

	/**
	* Set clob value.
	* 
	* @param clob $clob The clob to insert into database.
	*/
	function set_clob($clob)
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_clob');
		$this->conn->set_auto_commit(false);
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1); // pkey
		$stmt->set_string(2, "TestName");
		$stmt->set_clob(3, $clob);
		$stmt->execute_update();
		$this->conn->commit();
		$this->conn->set_auto_commit(true);
	} 

	/**
	* Note that limit & resultset scrolling behavior is extensively tested in result_set_test.
	*/
	function test_set_limit()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_limit(10);

		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());

		$this->assertEqual(10, $rs->get_record_count());
	} 

	function test_set_offset()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_limit(10);
		$stmt->set_offset(5);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());

		$rs->next();

		$this->assertEqual(6, $rs->get_int(1));

		$rs->close(); 
		// test setting offset w/ no limit
		$stmt->set_limit(0);
		$stmt->set_offset(5);
		
		if(!is_error($stmt->execute_query(result_set::FETCHMODE_NUM())))
			$this->fail("Expected sql_exception to be thrown when setting offset w/ no limit.");
		else
			$this->assertErrorPattern('/Cannot specify an offset without limit/');
			 
		// try changing it
		// try changing the offset info
		$stmt->set_offset(4);
		$stmt->set_limit(10);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());

		$rs->next();

		$this->assertEqual(5, $rs->get_int(1), 0, "Expected new first row to have changed after changing offset.");

		$stmt->close();
	} 
	
	/**
	* - test passing params to execute_query()
	* - test fetchmodes
	*/
	function test_execute_query()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.GET_BY_PKEY');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$rs = $stmt->execute_query(array(1), result_set::FETCHMODE_NUM());
		$rs->next();

		$this->assertIdentical(1, $rs->get_int(1));

		$rs->close(); 
		// make sure that getupdatecount returns null
		$this->assertTrue(($stmt->get_update_count() === null), "Expected getUpdateCount() to return NULL since last statement was a query.");

		$stmt->close();
	} 
	
	function test_execute_update()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_boolean');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->execute_update(array(true, 1));
		$this->assertEqual(1, $stmt->get_update_count());
		$this->assertTrue(($stmt->get_result_set() === null), "Expected getResultSet() to return NULL since last statement was an update.");
		$stmt->close();
	} 
	
	// Test the setters
	
	function test_set_array()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_array');
		$stmt = $this->conn->prepare_statement($exch->get_sql());

		$array = array("Hello", "Bob's", "Animals");

		$stmt->set_array(1, $array);
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_array');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();

		$this->assertIdentical($array, $rs->get_array(1));

		$rs->close();
		$stmt->close(); 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_array');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_array(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 

	function test_set_boolean()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_boolean');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_boolean(1, true);
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_boolean');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();

		$this->assertTrue($rs->get_boolean(1));

		$rs->close();
		$stmt->close(); 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_boolean');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_boolean(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 

	function test_set_date()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_date');
		$stmt = $this->conn->prepare_statement($exch->get_sql());

		$now = time();
		$stmt->set_date(1, $now);
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_date');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next(); 
		// we are only storing w/ date resolution, so we need to fix that
		$this->assertIdentical(date("d/m/Y", $now), $rs->get_date(1, "d/m/Y"), 0, "date() formatters did not produce expected results.");
		$this->assertIdentical(strftime("%x", $now), $rs->get_date(1, "%x"), 0, "strftime() formatters did not produce expected results.");

		$rs->close();
		$stmt->close(); 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_string'); 
		// intentionally using set_string query; the idea is to test the set_date() method, not the db's ability
		// to accept string in date col
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_date(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 

	function test_set_float()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_float');
		$stmt = $this->conn->prepare_statement($exch->get_sql());

		$stmt->set_float(1, 8.55);
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_float');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();

		$this->assertIdentical(8.55, $rs->get_float(1));

		$rs->close();
		$stmt->close(); 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_float');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_float(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 
	
	function test_set_int()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_int');
		$stmt = $this->conn->prepare_statement($exch->get_sql());

		$stmt->set_int(1, 50);
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_int');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertIdentical(50, $rs->get_int(1));

		$rs->close();
		$stmt->close(); 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_int');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 

	function test_set_null()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_null');
		$stmt = $this->conn->prepare_statement($exch->get_sql());

		$stmt->set_null(1);
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_null');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertNull($rs->get_int(1));

		$rs->close();
		$stmt->close();
	} 

	function test_set_string()
	{
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_string');
		$stmt = $this->conn->prepare_statement($exch->get_sql());

		$stmt->set_string(1, "Test String");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('prepared_statement_test.get_string');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertIdentical("Test String", $rs->get_string(1));

		$rs->close();
		$stmt->close(); 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_string');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_string(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 

	function test_set_time()
	{ 
		// coming soon...
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_string');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_time(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 

	function test_set_timestamp()
	{ 
		// Injection test.  Can we add a string that causes the db to generate an SQL error
		$exch = driver_test_manager::get_exchange('prepared_statement_test.set_string');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_time(1, "Normal TExt ' \' More # $%@ \\\\'''''\"\"'");
		$stmt->set_int(2, 1); // pkey
		$stmt->execute_update();
		$stmt->close();
	} 
} 

