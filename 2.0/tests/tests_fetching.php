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