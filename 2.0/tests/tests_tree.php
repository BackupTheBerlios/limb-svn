<?php

class tests_tree extends GroupTest 
{
	function tests_tree() 
	{
	  $this->GroupTest('tree');
	  $this->addTestFile(TEST_CASES_DIR . '/tree/test_nested_tree_creation.php');
	  $this->addTestFile(TEST_CASES_DIR . '/tree/test_nested_tree_query.php');
	  $this->addTestFile(TEST_CASES_DIR . '/tree/test_nested_tree_manipulation.php');
	}
}
?>