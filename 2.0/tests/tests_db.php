<?php

class tests_db extends GroupTest 
{
	function tests_db() 
	{
	  $this->GroupTest('db tests');
	  $this->addTestFile(TEST_CASES_DIR . '/db/test_db_mysql_typecast.php');
	  $this->addTestFile(TEST_CASES_DIR . '/db/test_db_mysql.php');
	  $this->addTestFile(TEST_CASES_DIR . '/db/test_db_table.php');
	  $this->addTestFile(TEST_CASES_DIR . '/db/test_db_table_cascade_delete.php');
	  $this->addTestFile(TEST_CASES_DIR . '/db/test_project_db_tables.php');
	}
}
?>