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


if(!defined('SIMPLE_TEST'))
	define('SIMPLE_TEST', 'c:/var/dev/unit_tests/trunk/simpletest/');

if ( !file_exists(SIMPLE_TEST . 'unit_tester.php') ) 
{
	die ( 'Problems: make sure the SIMPLE_TEST constant is set correctly in this file
	and that you have <a href="http://www.lastcraft.com/simple_test.php">Simple Test</a> installed');
}

require_once (SIMPLE_TEST . 'unit_tester.php');
require_once (SIMPLE_TEST . 'mock_objects.php');
require_once (SIMPLE_TEST . 'web_tester.php');
require_once (SIMPLE_TEST . 'reporter.php');

set_time_limit(300);

if (!defined('TEST_CASES_DIR'))
	define('TEST_CASES_DIR', 'c:/var/dev/limb/2.0/tests/cases/');


if (! defined('TEST_EXPECTED_OUTPUT_DIR'))
	define('TEST_EXPECTED_OUTPUT_DIR', './expected_output');
	
require_once('limb.setup.php');
require_once ('debug_mock.class.php');

?>