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
ob_start();
require_once(dirname(__FILE__) . '/setup.php');
require_once(dirname(__FILE__) . '/shop_root_group_test.class.php');
require_once(LIMB_DIR . '/tests/lib/html_test_runner.class.php');

$root_group =& new ShopRootGroupTest();
$test_runner =& new HTMLTestRunner();

$test_runner->run($root_group);
  
ob_end_flush();

?>