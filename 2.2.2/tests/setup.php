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
	define('SIMPLE_TEST', LIMB_DIR . '/tests/lib/simpletest1.0beta6/');

if ( !file_exists(SIMPLE_TEST . 'unit_tester.php') ) 
	die ('Make sure the SIMPLE_TEST constant is set correctly in this file');

require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'mock_objects.php');
require_once(SIMPLE_TEST . 'web_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');
	
require_once(LIMB_DIR . '/tests/lib/debug_mock.class.php');

set_time_limit(0);
error_reporting(E_ALL);

?>