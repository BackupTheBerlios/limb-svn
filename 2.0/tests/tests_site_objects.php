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
class tests_site_objects extends GroupTest 
{
	function tests_site_objects() 
	{
	  $this->GroupTest('site objects tests');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_site_object.php');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_site_object_manipulation.php');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_content_object_manipulation.php');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_user_object.php');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_file_object.php');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_image_object.php');
	  $this->addTestFile(TEST_CASES_DIR . '/site_objects/test_poll_container.php');
	}
}
?>