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

class test_table_info extends UnitTestCase
{
	var $conn;

	var $table;
	
	var $dbi;

	function setUp()
	{
		$this->conn = driver_test_manager::get_connection();
		$this->dbi = $this->conn->get_db_info();
		$this->table = $this->dbi->get_table("products");
	} 


	/**
	*/
	function test_get_columns()
	{
		$cols = $this->table->get_columns();
		$this->assertTrue(sizeof($cols) > 0);
	} 

	/**
	* * Test getting the products table
	*/
	function test_get_column()
	{
		$products = $this->dbi->get_table("products");
		$products2 = $this->dbi->get_table("Products");
		
		$this->assertTrue(
			($products->columns == $products2->columns) &&
			($products->foreign_keys == $products2->foreign_keys) &&
			($products->indexes == $products2->indexes) &&
			($products->primary_key == $products2->primary_key), "Expected get_table() to be case-insensitive.");
	} 
} 
