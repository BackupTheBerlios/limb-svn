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
  
if(!defined('VAR_DIR'))  
  define('VAR_DIR', dirname(__FILE__) . '/var/');

if(!defined('CONTENT_LOCALE_ID'))
  define('CONTENT_LOCALE_ID', 'en');
  
if(!defined('MANAGEMENT_LOCALE_ID'))
  define('MANAGEMENT_LOCALE_ID', 'en');

if(!defined('DEFAULT_MANAGEMENT_LOCALE_ID'))  
  define('DEFAULT_MANAGEMENT_LOCALE_ID', 'en');
  
if(!defined('DEFAULT_CONTENT_LOCALE_ID'))  
  define('DEFAULT_CONTENT_LOCALE_ID','en');

require_once(LIMB_DIR . '/setup.php');
require_once(LIMB_DIR . '/tests/lib/debug_mock.class.php');//don't move this line!!!

require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolvers_registry.inc.php');

register_file_resolver('ini',     LIMB_DIR . '/tests/lib/tests_ini_file_resolver');
register_file_resolver('action',  LIMB_DIR . '/tests/lib/tests_action_file_resolver');
register_file_resolver('strings', LIMB_DIR . '/tests/lib/tests_strings_file_resolver');
register_file_resolver('db_table',LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver');

require_once(LIMB_DIR . '/tests/setup_SimpleTest.inc.php');
require_once(LIMB_DIR . '/tests/lib/test_utils.php');
require_once(LIMB_DIR . '/tests/cases/limb_test_case.class.php');
require_once(LIMB_DIR . '/tests/lib/test_finder.class.php');
require_once(LIMB_DIR . '/class/lib/error/error.inc.php');

set_time_limit(0);
error_reporting(E_ALL);

?>