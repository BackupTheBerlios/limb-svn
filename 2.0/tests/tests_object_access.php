<?php

class tests_object_access extends GroupTest 
{
	function tests_object_access() 
	{
	  $this->GroupTest('object access tests');
	  $this->addTestFile(TEST_CASES_DIR . '/access/test_user.php');
	  $this->addTestFile(TEST_CASES_DIR . '/access/test_load_access_policy.php');
	  $this->addTestFile(TEST_CASES_DIR . '/access/test_save_access_policy.php');
	  $this->addTestFile(TEST_CASES_DIR . '/access/test_save_object_access_policy.php');
	  $this->addTestFile(TEST_CASES_DIR . '/access/test_access_templates.php');	  
	  $this->addTestFile(TEST_CASES_DIR . '/access/test_access_policy.php');
	}
}
?>