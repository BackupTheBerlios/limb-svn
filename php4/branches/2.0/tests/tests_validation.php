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


class tests_validation extends GroupTest 
{
	function tests_validation() 
	{
	  $this->GroupTest('validation tests');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/test_validator.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/test_error_list.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_single_field_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_required_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_size_range_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_email_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_match_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_tree_identifier_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_unique_user_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_unique_user_email_rule.php');
	  $this->addTestFile(TEST_CASES_DIR . '/validation/rules/test_locale_date_rule.php');
	}
}
?>