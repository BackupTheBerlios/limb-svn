<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(dirname(__FILE__) . '/setup.php');
require_once(dirname(__FILE__) . '/limb_root_group_test.class.php');
require_once(dirname(__FILE__) . '/lib/cli_test_runner.class.php');

$root_group = new LimbRootGroupTest();
$test_runner = new CLITestRunner();

$test_runner->run($root_group);

?>