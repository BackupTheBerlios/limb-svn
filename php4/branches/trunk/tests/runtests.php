<?php
require_once(dirname(__FILE__) . '/setup.php');
require_once(dirname(__FILE__) . '/limb_root_group_test.class.php');
require_once(LIMB_DIR . '/tests/lib/cli_test_runner.class.php');

$root_group = new LimbRootGroupTest();
$test_runner = new CLITestRunner();

$test_runner->run($root_group);

?>