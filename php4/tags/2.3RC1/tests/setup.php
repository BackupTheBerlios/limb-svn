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
@define('SIMPLE_TEST', dirname(__FILE__) . '/../../../../external/php_simpletest_1.0RC1/');

if (!file_exists(SIMPLE_TEST . 'unit_tester.php') )
  die ('Make sure the SIMPLE_TEST constant is set correctly in this file');

if(!defined('LIMB_DIR'))
  define('LIMB_DIR', dirname(__FILE__) . '/../');

require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'mock_objects.php');
require_once(SIMPLE_TEST . 'web_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');

require_once(LIMB_DIR . '/tests/cases/limb_group_test.class.php');
require_once(LIMB_DIR . '/tests/cases/limb_test_case.class.php');
require_once(LIMB_DIR . '/tests/lib/test_finder.class.php');
require_once(LIMB_DIR . '/tests/lib/debug_mock.class.php');
require_once(LIMB_DIR . '/tests/lib/test_utils.php');

set_time_limit(0);
error_reporting(E_ALL);

?>