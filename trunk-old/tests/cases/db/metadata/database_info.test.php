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

class database_info_test extends UnitTestCase
{
	var $conn;

	var $dbi;

	function setUp()
	{
		$this->conn = driver_test_manager::get_connection();
		$this->dbi = $this->conn->get_db_info();
	} 

	/**
	* Make sure at least "products" table is in table list.
	*/
	function test_get_tables()
	{
		$tables = $this->dbi->get_tables();
		$this->assertTrue(count($tables) >= 1, "Expected at least one table ('products')from get_tables() call.");
	} 

	/**
	* * Test getting the products table
	*/
	function test_get_table()
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
