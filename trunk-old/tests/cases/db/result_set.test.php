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

require_once(LIMB_DIR . '/tests/cases/db/prepared_statement.test.php');

SimpleTestOptions::ignore('result_set_test');

class test_result_set extends UnitTestCase
{
	var $conn;
	
	function test_result_set()
	{
		parent :: UnitTestCase();

		$this->conn = driver_test_manager::get_connection();
	}

	/**
	* Initialize the default resultset.
	* Not all methods need this initialized.
	*/
	function all_rs()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		return $rs;
	} 

	/**
	* Test the get_record_count method.  Note that this will not work w/
	* unbuffered result sets ... e.g. I think Oracle.
	*/
	function test_get_record_count()
	{ 
		// SELECT COUNT(*) ...
		$exch1 = driver_test_manager::get_exchange('record_count');
		$rs = $this->conn->execute_query($exch1->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$expected = $rs->get_int(1); 
		// SELECT * ...
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$rs = $this->conn->execute_query($exch->get_sql());
		$this->assertEqual($expected, $rs->get_record_count());
	} 

	function test_fetchmode_num()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$fields = $rs->get_row();
		$this->assertTrue(array_key_exists("0", $fields));
		$this->assertTrue(!array_key_exists("ProductID", $fields));
		$rs->close();
	} 

	function test_fetchmode_assoc()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_ASSOC());
		$rs->next();
		$fields = $rs->get_row();
		$keys = array_keys($fields);
		$this->assertTrue(!array_key_exists("0", $fields), "Expected not to find '0' in fields array.");
		$this->assertEqual("productid" , $keys[0], "Expected to find lcase column name in field array.");
		$rs->close();
	} 

	/**
	* Test an ASSOC fetch with a connection that has the db_factory::NO_ASSOC_LOWER flag set.
	*/
	function test_fetchmode_assoc_no_change()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');

		$conn2 = db_factory::get_new_connection(driver_test_manager::get_dsn(), db_factory::NO_ASSOC_LOWER());
		driver_test_manager::init_db($conn2);

		$rs = $conn2->execute_query($exch->get_sql(), result_set::FETCHMODE_ASSOC());
		$rs->next();
		$keys = array_keys($rs->get_row());
		$this->assertEqual("ProductID", $keys[0], "Expected to find mixed-case column name.");
		$rs->close(); 
		// do NOT close the connection; in many cases both connection objects will share
		// the same db connection
	} 

	/**
	* Test next() and bounded result sets.
	* We test to make sure that next() will loop until the end.
	*/
	function test_next()
	{
		$rs = $this->all_rs();
		$i = 0;
		while ($rs->next()) $i++;
		$this->assertEqual($rs->get_record_count(), $i);

		$rs->close();
	} 

	/**
	* Ensures that results are no longer available after
	* closing a resultset.
	*/
	function test_close()
	{
		$rs = $this->all_rs();
		$rs->next();
		$rs->close();
		
		if(!is_error($rs->get(1)))
			$this->fail("Expected sql_execption to be thrown for invalid column after closing ResultSet.");
		else
			$this->assertErrorPattern("/Invalid resultset column/");
	} 

	/**
	* Test behavior of seek().  Note that the return results of seek
	* are not reliable for determining whether a cursor position exists.
	*/
	function test_seek()
	{
		$rs = $this->all_rs();

		$rs->seek(0);
		$rs->next();

		$this->assertEqual(1, $rs->get_int(1));
		$rs->seek(3);
		$this->assertEqual(1, $rs->get_int(1), 0, "Expected to still find same value for get(1), since seek() isn't supposed to load row.");
		$rs->next();
		$this->assertEqual(4, $rs->get_int(1), 0, "Expected next() to now fetch 4 after call to seek(3)");

		$rs->close();
	} 

	function test_is_before_first()
	{
		$rs = $this->all_rs(); 
		// before calling next() we can expect RS to be before first
		$this->assertTrue($rs->is_before_first());

		$rs->close();
	} 

	function test_is_after_last()
	{
		$rs = $this->all_rs();
		while ($rs->next()); // advance to end        
		$this->assertTrue($rs->is_after_last());

		$rs->close();
	} 
	
	// these are not scrolling functions:
	function test_before_first()
	{
		$rs = $this->all_rs();
		for($i = 0;$i < 10;$i++) // advance a few positions
		{
			$rs->next();
		} 

		$rs->before_first();
		$this->assertTrue($rs->is_before_first());

		$rs->close();
	} 

	function test_after_last()
	{
		$rs = $this->all_rs();
		for($i = 0;$i < 10;$i++) // advance a few positions
		{
			$rs->next();
		} 
		$rs->after_last();
		$this->assertTrue($rs->is_after_last());

		$rs->close();
	} 
	
	// scrolling functions -- do not work w/ all RDBMS, so must be overridden when applicable
	// 
	function test_previous()
	{
		$rs = $this->all_rs(); 
		// advance to the fifth record, which will have product_id of 5
		for($i = 0;$i < 5;$i++) $rs->next();

		$this->assertEqual(5, $rs->get_int(1));

		$rs->previous();

		$this->assertEqual(4, $rs->get_int(1)); 
		// now keep going back until false
		while ($rs->previous());

		$this->assertTrue($rs->is_before_first());

		$rs->close();
	} 

	function test_relative()
	{
		$rs = $this->all_rs();

		$rs->next(); // advance one record 
		// move ahead 5 spaces
		$rs->relative(5);
		$this->assertEqual(6, $rs->get_int(1));

		$rs->relative(-2);
		$this->assertEqual(4, $rs->get_int(1));

		$res = $rs->relative(200);
		$this->assertTrue($rs->is_after_last());
		$this->assertFalse($res, "relative() should return false if offset after end of recordset"); 
		
		$res = $rs->relative(-200);
		$this->assertTrue($rs->is_before_first());
		$this->assertFalse($res, "relative() should return false if offset before start of recordset"); 
		
		$rs->relative(2);
		$this->assertEqual(2, $rs->get_int(1));

		$rs->close();
	} 

	function test_absolute()
	{
		$rs = $this->all_rs(); 
		// advance to the fifth record, which will have product_id of 5
		$rs->absolute(5);
		$this->assertEqual(5, $rs->get_int(1));

		$rs->absolute(50);
		$this->assertEqual(50, $rs->get_int(1));

		$res = $rs->absolute(300);
		$this->assertTrue($rs->is_after_last());
		$this->assertFalse($res, "absolute() should return false if pos is after end of recordset"); // returns false if offset is after last or before first 
		// $this->expect_warning('Offset after end of recordset', $rs);
		$res = $rs->absolute(0);
		$this->assertTrue($rs->is_before_first());
		$this->assertFalse($res, "absolute() should return false if offset is before start of recordset"); // returns false if offset is after last or before first 
		// $this->expect_warning('Offset before start of recordset', $rs);
		$res = $rs->absolute(-2);
		$this->assertTrue($rs->is_before_first());
		$this->assertFalse($res, "absolute() should return false if offset is before start of recordset"); // returns false if offset is after last or before first 
		// $this->expect_warning('Offset before start of recordset', $rs);
		$rs->close();
	} 

	function test_first()
	{
		$rs = $this->all_rs();

		$exch = driver_test_manager::get_exchange('result_set_test.MIN_ID');
		$min_rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$min_rs->next();
		$min = $min_rs->get(1);

		$rs->first();
		$this->assertEqual($min, $rs->get(1));

		$rs->close();
	} 

	function test_last()
	{
		$rs = $this->all_rs();

		$exch = driver_test_manager::get_exchange('result_set_test.MAX_ID');
		$max_rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$max_rs->next();
		$max = $max_rs->get(1);

		$rs->last();
		$this->assertEqual($max, $rs->get(1));

		$rs->close();
	} 

	/**
	* This test is primarily to test emulated LIMIT/OFFSET. 
	* 
	* It will, of course, test the natively supported LIMIT/OFFSET, but
	* the real potential for issues lies in the drivers that emulate these.
	* 
	* This class only uses forward-scrolling cursor functions.
	* 
	*/
	function test_limit()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$stmt = $this->conn->create_statement();
		$stmt->set_limit(10);
		$stmt->set_offset(5); 
		// 1) make sure contains right number of rows
		$rs1 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$count = 0;
		while ($rs1->next()) $count++;
		$this->assertEqual(10, $count, 0, "LIMITed resultset contains wrong number of rows.");
		$rs1->close();
		unset($rs1); 
		// 2) make sure that first record is the correct one
		// using next()
		$rs2 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs2->next(); 
		// first() relative() and absolute() handled by test_limit_scroll_backwards()
		$this->assertEqual(6, $rs2->get_int(1), 0, "LIMITed resultset starts on the wrong row.");
		$rs2->close();
		unset($rs2); 
		// 3) make sure that the last record is the correct one.
		$rs3 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		while ($rs3->next())
		{
			$last = $rs3->get_int(1);
		} 
		$this->assertEqual(15, $last, 0, "LIMITed resultset ends on the wrong row.");
		$rs3->close();
		unset($rs3);

		$rs4 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs4->last();
		$this->assertEqual(15, $rs4->get_int(1), 0, "LIMITed resultset ends on the wrong row.");
		$rs4->close();
		unset($rs4); 
		// 4) make sure that the relative() and absolute() (forward) method will report appropriate end
		$rs5 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$res = $rs5->absolute(11); 
		// $this->expect_warning('Offset after end of recordset',$rs5);
		$this->assertFalse($res, "absolute() should return false when after end of resultset");
		$rs5->close();
		unset($rs5);

		$rs6 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$res = $rs6->relative(11);
		$this->assertFalse($res, "relative() should return false when after end of resultset"); 
		// $this->expect_warning('Offset after end of recordset', $rs6);
		$rs6->close();
		unset($rs6);

		$stmt->close();
	} 

	/**
	* Continues LIMIT tests, but using backwards-scrolling methods.
	* 
	* Some RDBMS drivers don't support backwards scrolling; they'll need
	* to override this method.
	*/
	function test_limit_scroll_backwards()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.ALL_RECORDS');
		$stmt = $this->conn->create_statement();
		$stmt->set_limit(10);
		$stmt->set_offset(5); 
		// using next()
		$rs2 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs2->first();
		$this->assertEqual(6, $rs2->get_int(1), 0, "LIMITed resultset starts on the wrong row.");
		$rs2->close();
		unset($rs2);

		$rs3 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		for($i = 0;$i < 3;$i++) $rs3->next(); // move ahead 3 spaces            
		$res = $rs3->relative(-4);
		$this->assertFalse($res, "relative() should return false when before start of resultset"); 
		// $this->expect_warning('Offset before start of recordset', $rs3);
		$rs3->close();
		unset($rs3);

		$rs4 = $stmt->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		for($i = 0;$i < 3;$i++) $rs4->next(); // move ahead 3 spaces            
		$res = $rs4->absolute(-1);
		$this->assertFalse($res, "absolute() should return false when before start of resultset"); 
		// $this->expect_warning('Offset before start of recordset', $rs4);
		$rs4->close();
		unset($rs4);

		$stmt->close();
	} 

	// column accessors -- many of these will be overridden in driver classes so
	// that derived values can be checked against native values in DB.
	
	function test_get()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.SINGLE_RECORD');
		$rs = $this->conn->execute_query(sprintf($exch->get_sql(), 1), result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertEqual(1, $rs->get_int(1));

		$rs->close();
	} 

	function test_get_array()
	{ 
		// coming soon
	} 

	function test_get_boolean()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_boolean.FALSE');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertIdentical($rs->get_boolean(1), false, "Expected answer to be false, was: " . $rs->get_boolean(1)); 
		// avoid using absolute() or relative() because not all drivers support it.
		$exch = driver_test_manager::get_exchange('result_set_test.get_boolean.TRUE');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertIdentical($rs->get_boolean(1), true);
		
		if(!is_error($rs->get_boolean("productid")))
		{
			$this->fail("Expected sql_execption to be thrown for invalid column.");
		} 
		else
		{
			$this->assertErrorPattern("/Invalid resultset column/");
		} 

		$rs->close();
	} 

	function get_blob()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_blob');
		
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$b = $rs->get_blob(1);
		$rs->close();
		return $b;
	} 

	function get_clob()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_clob');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$c = $rs->get_clob(1);
		$rs->close();
		return $c;
	} 

	/**
	* This function depends on ability to set Blob values -- so 
	* prepared_statement::set_blob() is also implicitly tested.
	*/
	function test_get_blob()
	{
		$pst = new test_prepared_statement();
		$b1 = $pst->create_blob();
		$pst->set_blob($b1);

		$b2 = $this->get_blob();
		$this->assertEqual(strlen($b1->get_contents()), strlen($b2->get_contents()), 0, "BLOB lengths do not match.");
		$this->assertEqual(md5($b1->get_contents()), md5($b2->get_contents()), 0, "BLOB contents do not match.");
	} 

	/**
	* This function depends on ability to set Blob values -- so 
	* prepared_statement::set_blob() is also implicitly tested.
	*/
	function test_get_clob()
	{
		$pst = new test_prepared_statement();
		$b1 = $pst->create_clob();
		$pst->set_clob($b1);

		$b2 = $this->get_clob();
		$this->assertEqual(strlen($b1->get_contents()), strlen($b2->get_contents()), 0, "CLOB lengths do not match.");
		$this->assertEqual(md5($b1->get_contents()), md5($b2->get_contents()), 0, "CLOB contents do not match.");
	} 

	function test_get_date()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_date');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();

		$result_ts = strtotime($exch->get_result());
		$ts = (int) $rs->get_date(1, "U");

		$this->assertEqual($result_ts, $ts);

		$this->assertEqual(strftime("%x", $result_ts), $rs->get_date(1, "%x"));

		if(!is_error($rs->get_date("orderdate")))
		{
			$this->fail("Expected sql_exception to be thrown for invalid column.");
		} 
		else
		{
			$this->assertErrorPattern("/Invalid resultset column/");
		} 

		$rs->close(); 
		
		// try w/ invalid date
		$exch = driver_test_manager::get_exchange('result_set_test.get_string');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		
		if(!is_error($rs->get_date(1)))
		{
			$this->fail("Expected sql_execption to be thrown for bad date type.");
		} 
		else
		{
			$this->assertErrorPattern("/Unable to convert value/");
		} 

		$rs->close();
	} 
//
	function test_get_float()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_float');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();

		$exp_val = (float) $exch->get_result();

		$this->assertIdentical($exp_val, $rs->get_float(1));

		if(!is_error($rs->get_float("UnitPrice")))
		{
			$this->fail("Expected sql_execption to be thrown for invalid column.");
		} 
		else
		{
			$this->assertErrorPattern("/Invalid resultset column/");
		} 

		$rs->close();
	} 

	function test_get_int()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_int');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();

		$exp_val = (int) $exch->get_result();

		$this->assertIdentical($exp_val, $rs->get_int(1));

		if(!is_error($rs->get_int("UnitsOnOrder")))
		{
			$this->fail("Expected sql_execption to be thrown for invalid column.");
		} 
		else
		{
			$this->assertErrorPattern("/Invalid resultset column/");
		} 

		$rs->close();
	} 

	function test_get_string()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.get_string');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();

		$exp_val = $exch->get_result();

		$this->assertIdentical($exp_val, $rs->get_string(1));

		if(!is_error($rs->get_string("ProductName")))
		{
			$this->fail("Expected sql_execption to be thrown for invalid column.");
		} 
		else
		{
			$this->assertErrorPattern("/Invalid resultset column/");
		} 

		$rs->close();
	} 

	function test_get_time()
	{ 
		// coming soon ...
		// try w/ invalid time
		$exch = driver_test_manager::get_exchange('result_set_test.get_string');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		
		if(!is_error($rs->get_time(1)))	
		{
			$this->fail("Expected sql_execption to be thrown for bad date type.");
		} 
		else
		{
			$this->assertErrorPattern("/Unable to convert value/");
		} 
	} 

	function test_get_timestamp()
	{ 
		// coming soon ...
		// try w/ invalid timestamp
		$exch = driver_test_manager::get_exchange('result_set_test.get_string');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		
		if(!is_error($rs->get_timestamp(1)))
		{
			$this->fail("Expected sql_execption to be thrown for bad date type.");
		} 
		else
		{
			$this->assertErrorPattern("/Unable to convert value/");
		} 
	} 

	/**
	* Make sure that get() and get_string() are returning properly rtrimmed results.
	*/
	function test_trimmed_get()
	{
		$exch = driver_test_manager::get_exchange('result_set_test.set_string.RTRIM');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_string(1, "TEST    ");
		$stmt->set_int(2, 1);
		$stmt->execute_update();
		$stmt->close();

		$exch = driver_test_manager::get_exchange('result_set_test.get_string.RTRIM');
		$stmt = $this->conn->prepare_statement($exch->get_sql());
		$stmt->set_int(1, 1);
		$rs = $stmt->execute_query(result_set::FETCHMODE_NUM());
		$rs->next();
		$this->assertEqual("TEST", $rs->get_string(1));

		$stmt->close();
		$rs->close();
	} 
} 
