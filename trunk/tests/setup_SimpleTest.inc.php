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
require_once(LIMB_DIR . '/core/lib/util/ini.class.php');

if(!$SimpleTest_dir = get_ini_option('external.ini', 'library_path', 'SimpleTest'))
  $SimpleTest_dir = '../external/SimpleTest/';
  
define('SIMPLE_TEST', $SimpleTest_dir);

if ( !file_exists(SIMPLE_TEST . 'unit_tester.php') ) 
	die ('Make sure the SIMPLE_TEST constant is set correctly in this file');

require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'mock_objects.php');
require_once(SIMPLE_TEST . 'reporter.php');

?>