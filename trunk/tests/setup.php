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
if(!defined('LIMB_DIR'))
  define('LIMB_DIR', dirname(__FILE__) . '/../');
  
if(!defined('PROJECT_DIR'))
  define('PROJECT_DIR', LIMB_DIR);
    
require_once(PROJECT_DIR . 'setup.php');

if(!defined('DB_TYPE'))
  define('DB_TYPE', 'mysql');

if(!defined('DB_HOST'))
  define('DB_HOST', '192.168.0.6');

if(!defined('DB_LOGIN'))
  define('DB_LOGIN', 'root');

if(!defined('DB_PASSWORD'))
  define('DB_PASSWORD', 'test');

if(!defined('DB_NAME'))
  define('DB_NAME', 'limb_trunk_tests');

if(!defined('SIMPLE_TEST'))
	define('SIMPLE_TEST', 'c:/var/external/php_simple_test_1.0RC1/');

if(!defined('CONTENT_LOCALE_ID'))
  define('CONTENT_LOCALE_ID', 'en');
  
if(!defined('MANAGEMENT_LOCALE_ID'))
  define('MANAGEMENT_LOCALE_ID', 'en');

if(!defined('DEFAULT_MANAGEMENT_LOCALE_ID'))  
  define('DEFAULT_MANAGEMENT_LOCALE_ID', 'en');
  
if(!defined('DEFAULT_CONTENT_LOCALE_ID'))  
  define('DEFAULT_CONTENT_LOCALE_ID','en');
  	
if ( !file_exists(SIMPLE_TEST . 'unit_tester.php') ) 
	die ('Make sure the SIMPLE_TEST constant is set correctly in this file');

require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'mock_objects.php');
require_once(SIMPLE_TEST . 'reporter.php');

require_once(LIMB_DIR . '/tests/lib/test_utils.php');
require_once(LIMB_DIR . '/tests/lib/debug_mock.class.php');
require_once(LIMB_DIR . '/tests/cases/limb_test_case.class.php');

set_time_limit(0);
error_reporting(E_ALL);

?>