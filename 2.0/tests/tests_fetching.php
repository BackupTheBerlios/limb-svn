<?php

class tests_fetching extends GroupTest 
{
	function tests_fetching() 
	{
	  $this->GroupTest('fetch operations');
	  $this->addTestFile(TEST_CASES_DIR . '/fetching/test_site_object_fetch.php');
	  $this->addTestFile(TEST_CASES_DIR . '/fetching/test_site_object_fetch_accessible.php');
	  $this->addTestFile(TEST_CASES_DIR . '/fetching/test_content_object_fetch.php');
	  $this->addTestFile(TEST_CASES_DIR . '/fetching/test_fetching.php');
	}
}
?>