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

SimpleTestOptions::ignore('test_id_generator');

class test_id_generator extends UnitTestCase
{
	var $conn;

	var $idgen;

	/**
	* Re-initialize the database.
	* 
	* We only need to do this in set_up() method -- not in every invocation of this class --
	* since the result_set methods do not modify the db.
	*/
	function setUp()
	{
		driver_test_manager::restore();
		$this->conn = driver_test_manager::get_connection();
		$this->idgen = $this->conn->get_id_generator();
	} 

	function test_is_before_insert()
	{
		$type = $this->idgen->get_id_method();
		if ($type === id_generator::SEQUENCE())
		{
			$this->assertTrue($this->idgen->is_before_insert());
		} 
		else
		{
			$this->assertFalse($this->idgen->is_before_insert());
		} 
	} 

	function test_is_after_insert()
	{
		$type = $this->idgen->get_id_method();
		if ($type === id_generator::AUTOINCREMENT())
		{
			$this->assertTrue($this->idgen->is_after_insert());
		} 
		else
		{
			$this->assertFalse($this->idgen->is_after_insert());
		} 
	} 

	function test_get_id()
	{ 
		$exch = driver_test_manager::get_exchange('id_generator_test.get_id.INIT');
		$rs = $this->conn->execute_query($exch->get_sql(), result_set::FETCHMODE_NUM());
		$rs->next();
		$max = $rs->get_int(1);

		$key_info = 'idgentest_seq';

		if ($this->idgen->get_id_method() === id_generator::SEQUENCE())
		{
			$exch = driver_test_manager::get_exchange('id_generator_test.get_id.SEQUENCE');
			$id = $this->idgen->get_id($key_info);
			$stmt = $this->conn->prepare_statement($exch->get_sql());
			$stmt->execute_update(array($id, 'Test'));
		} 
		else
		{
			$exch = driver_test_manager::get_exchange('id_generator_test.get_id.AUTOINCREMENT');
			$stmt = $this->conn->prepare_statement($exch->get_sql());
			$stmt->execute_update(array('Test'));
			$id = $this->idgen->get_id($key_info);
		} 

		$this->assertEqual($max + 1, $id, 0, "Next id was not max + 1");
	} 
} 
