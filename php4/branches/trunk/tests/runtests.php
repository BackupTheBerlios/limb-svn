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
require_once(dirname(__FILE__) . '/setup.php');
require_once(dirname(__FILE__) . '/lib/limb_cli_reporter.class.php');

function usage()
{
	$usage = <<<EOD
Usage: ./runtests.php [OPTION]...
Run the LIMB unit tests. If ALL of the test cases pass a count of total
passes is printed on STDOUT. If ANY of the test cases fail (or raise
errors) details are printed on STDERR and this script returns a non-zero
exit code.
  -g  --group=PATH        specify a grouptest path. If no grouptest is
                          specified, all test cases will be run.
  -l  --list=PATH         list available grouptests files
  -p, --path              path to SimpleTest installation
  -h, --help              display this help and exit

EOD;
	echo $usage;
	exit(0);
} 

/* test options */
$opt_list_path = false;
$opt_casefile = false;
$opt_group_path = false;

/* only allow cmd line options if PEAR Console_Getopt is available */
require_once ('Console/Getopt.php');
/* PEAR lib */

if (class_exists('Console_Getopt'))
{
	$argv = Console_Getopt::readPHPArgv();
	if (PEAR::isError($argv))
	{
		die('Fatal Error: ' . $argv->getMessage()) . "\n";
	} 
	
	$short_opts = "f:g:hls:p:";
	$long_opts = array("help", "file=", "group=", "list", "separator=", "path=");
	$options = Console_Getopt::getopt($argv, $short_opts, $long_opts);
	if (PEAR::isError($options))
	{
		usage($available_grouptests);
	} 
	
	foreach ($options[0] as $option)
	{
		switch ($option[0])
		{
			case 'h':
			case '--help':
				usage();
				break;
			case 'g':
			case '--group':
				$opt_group_path = $option[1];
				break;
			case 'l':
			case 'list':
				$opt_list_path = $option[1];
				break;
			case 's':
			case 'separator':
				$opt_separator = $option[1];
				break;
			case 'p':
			case 'path':
				if (file_exists($option[1]))
				{
					define('SIMPLE_TEST', $option[1]);
				} 
				break;
		} 
	} 
} 

$global_group = new GlobalGroupTest();
/* list grouptests */
if ($opt_list_path)
{
  $current_group = CLITestManager::getGroupTestList($opt_list_path, $global_group);
  $cases = $current_group->getTestCasesHandles();
  
	echo CLITestManager::getGroupTestList(TEST_GROUPS_DIR);
	exit(0);
} 
/* run a test case */
if ($opt_casefile !== false)
{
	TestManager::runTestCase($opt_casefile, new LimbCLIReporter());
	echo debug :: parse_cli_console();
	exit(0);
} 
/* run a grouptest */
if ($opt_group_path !== false)
{  
  $groups = TestManager::getGroupTestList(TEST_GROUPS_DIR);
  if(isset($groups[$opt_group_path]))
  {
	  TestManager::runGroupTest($groups[$opt_group_path], new LimbCLIReporter());
	  echo debug :: parse_cli_console();	
	}
	exit(0);
} 
/* run all tests */
TestManager::runAllTests(TEST_GROUPS_DIR, new LimbCLIReporter());
echo debug :: parse_cli_console();
exit(0);

?>