<?php

class tests_image_library extends GroupTest 
{
	function tests_image_library() 
	{
	  $this->GroupTest('Image library');
	  $this->addTestFile(TEST_CASES_DIR . '/image/test_gd_library.php');
	  $this->addTestFile(TEST_CASES_DIR . '/image/test_netpbm_library.php');
	}
}
?>