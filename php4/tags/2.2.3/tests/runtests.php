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
require_once(LIMB_DIR . '/tests/setup.php');
require_once(LIMB_DIR . '/tests/lib/test_manager.php');
require_once(LIMB_DIR . '/core/lib/error/error.inc.php');

function usage()
{
	$usage = <<<EOD
Usage: ./runtests.php [OPTION]...
Run the WACT unit tests. If ALL of the test cases pass a count of total
passes is printed on STDOUT. If ANY of the test cases fail (or raise
errors) details are printed on STDERR and this script returns a non-zero
exit code.
  -f  --file=NAME         specify a test case file
  -g  --group=NAME        specify a grouptest. If no grouptest is
                          specified, all test cases will be run.
  -l  --list              list available grouptests/test case files
  -s, --separator=SEP     set the character(s) used to separate fail
                          details to SEP
  -p, --path              path to SimpleTest installation
  -h, --help              display this help and exit

EOD;
	echo $usage;
	exit(0);
} 

/* test options */
$opt_separator = '->';
$opt_list = false;
$opt_casefile = false;
$opt_groupfile = false;

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
			case 'f':
			case '--file':
				$opt_casefile = $option[1];
				break;
			case 'g':
			case '--group':
				$opt_groupfile = $option[1];
				break;
			case 'l':
			case 'list':
				$opt_list = true;
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

if (!@ include_once(SIMPLE_TEST . 'runner.php'))
{
	error('runtime', 'LIBRARY_REQUIRED', array('library' => 'Simple Test',
			'path' => SIMPLE_TEST));
} 

require_once(LIMB_DIR . '/tests/lib/cli_reporter.php');

/* list grouptests */
if ($opt_list)
{
	echo CLITestManager::getGroupTestList(TEST_GROUPS_DIR);
	echo CLITestManager::getTestCaseList(TEST_CASES_DIR);
	exit(0);
} 
/* run a test case */
if ($opt_casefile)
{
	TestManager::runTestCase($opt_casefile, new CLIReporter($opt_separator));
	echo debug :: parse_cli_console();
	exit(0);
} 
/* run a grouptest */
if ($opt_groupfile)
{
	TestManager::runGroupTest($opt_groupfile, TEST_GROUPS_DIR,
		new CLIReporter($opt_separator));
	echo debug :: parse_cli_console();	
	exit(0);
} 
/* run all tests */
TestManager::runAllTests(new CLIReporter($opt_separator));
echo debug :: parse_cli_console();
exit(0);

?>