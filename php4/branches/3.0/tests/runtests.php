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
require_once(dirname(__FILE__) . '/LimbRootGroupTest.class.php');
require_once(dirname(__FILE__) . '/lib/CliTestRunner.class.php');

$root_group = new LimbRootGroupTest();
$test_runner = new CLITestRunner();

$test_runner->run($root_group);

?>