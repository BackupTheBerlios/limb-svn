<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
ob_start();
require_once(dirname(__FILE__) . '/setup.php');
require_once(dirname(__FILE__) . '/FullTextSearchRootGroupTest.class.php');
require_once(LIMB_DIR . '/tests/lib/HtmlTestRunner.class.php');

$root_group = new FullTextSearchRootGroupTest();
$test_runner = new HTMLTestRunner();

$test_runner->run($root_group);

ob_end_flush();

?>