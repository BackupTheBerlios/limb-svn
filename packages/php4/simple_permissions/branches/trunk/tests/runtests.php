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
require_once(dirname(__FILE__) . '/setup.php');
require_once(dirname(__FILE__) . '/SimplePermissionsRootGroupTest.class.php');
require_once(LIMB_DIR . '/tests/lib/CliTestRunner.class.php');

$root_group = new SimplePermissionsRootGroupTest();
$test_runner = new CLITestRunner();

$test_runner->run($root_group);

?>