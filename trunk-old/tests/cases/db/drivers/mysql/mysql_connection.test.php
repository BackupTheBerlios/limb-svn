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
require_once(LIMB_DIR . '/tests/cases/db/connection.test.php');

class test_mysql_connection extends test_connection
{
	var $test_transactions = false;

	function setUp()
	{
		parent::setUp(); 
		// check the table types
		$sql = "SHOW TABLE STATUS";
		$rs = $this->conn->execute_query($sql);
		while ($rs->next())
		{
			$row = $rs->get_row();
			if ($row['name'] == 'products')
			{
				if ($row['type'] == 'InnoDB' || $row['Type'] == 'BDB')
				{
					$this->test_transactions = true;
				} 
				break; // we don't care about the other tables.
			} 
		} 
		$rs->close();
	} 

	function test_set_auto_commit()
	{
		if ($this->test_transactions)
		{
			parent::test_set_auto_commit();
		} 
	} 

	function test_commit()
	{
		if ($this->test_transactions)
		{
			parent::test_commit();
		} 
	} 

	function test_rollback()
	{
		if ($this->test_transactions)
		{
			parent::test_rollback();
		} 
	} 

	/**
	* Test the apply_limit() method.  By default this method will not modify the values provided.
	* Subclasses must override this method to test for appropriate SQL modifications.
	*/
	function test_apply_limit()
	{
		$sql = "SELECT * FROM sampletable WHERE category = 5";
		$offset = 5;
		$limit = 50;

		$this->conn->apply_limit($sql, $offset, $limit);

		$this->assertWantedPattern("/^SELECT \* FROM sampletable WHERE category = 5\s*?LIMIT\s*?50\s*?OFFSET\s*?5$/", $sql);
	} 
} 
