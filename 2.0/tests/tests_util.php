<?php

class tests_util extends GroupTest 
{
	function tests_util() 
	{
	  $this->GroupTest('util tests');
	  $this->addTestFile(TEST_CASES_DIR . '/util/test_log.php');
	  $this->addTestFile(TEST_CASES_DIR . '/util/test_ini.php');
	  $this->addTestFile(TEST_CASES_DIR . '/util/test_swf_file.php');
	}
}
?>