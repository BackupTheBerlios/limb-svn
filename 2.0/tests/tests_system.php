<?php

class tests_system extends GroupTest 
{
	function tests_system() 
	{
	  $this->GroupTest('system tests');
	  $this->addTestFile(TEST_CASES_DIR . '/system/test_dir.php');
	}
}
?>