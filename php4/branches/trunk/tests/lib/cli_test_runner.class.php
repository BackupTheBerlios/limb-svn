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
require_once(dirname(__FILE__) . '/test_runner.class.php');
require_once(dirname(__FILE__) . '/limb_cli_reporter.class.php');
require_once ('Console/Getopt.php');

class CLITestRunner extends TestRunner
{ 
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
  
  function run(&$root_group)
  {
    $opt_browse_path = false;
    $opt_test_path = false;
    
  	$argv = Console_Getopt::readPHPArgv();
  	if (PEAR::isError($argv))
  	{
  		die('Fatal Error: ' . $argv->getMessage()) . "\n";
  	} 
  	
  	$short_opts = 't:hb:p:';
  	$long_opts = array('help', 'test=', 'browse', 'path=');
  	$options = Console_Getopt::getopt($argv, $short_opts, $long_opts);
  	if (PEAR::isError($options))
  	{
  		$this->usage();
  	} 
  	
  	foreach ($options[0] as $option)
  	{
  		switch ($option[0])
  		{
  			case 'h':
  			case '--help':
  				$this->usage();
  				break;
  			case 't':
  			case '--test':
  				$opt_test_path = $option[1];
  				break;
  			case 'b':
  			case 'browse':
  				$opt_browse_path = $option[1];
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
    
    if ($opt_browse_path)
      $this->browse($opt_browse_path, $root_group);
    
    if ($opt_test_path)
      $this->perform($opt_test_path, $root_group);
      
    if(!$opt_browse_path && !$opt_test_path)
      $this->browse('', $root_group);
    
    exit(0);
  }
  
  function &_getReporter()
  {
    return new LimbCLIReporter();
  }
  
  function getBaseUrl()
  {
    return $_SERVER['PHP_SELF'];
  }
 
  function _displayPerform($path, &$root_group, &$current_group)
  {  
    echo '';
  }
  
  function _displayBrowse($path, &$root_group, &$current_group)
  {
    if (is_a($current_group, 'LimbGroupTest'))
		  $group_tests = $current_group->getTestCasesHandles();
		else  
		  $group_tests = array();
    
		$buffer = "Available test cases in " . $current_group->getLabel() . " :\n";
		
		if (sizeof($group_tests))
		{		
  		foreach ($group_tests as $index => $group_test)
  		{
		    resolve_handle($group_test);
		  
  			$buffer .= $path . '/' . $index . ' ' . $group_test->getLabel() . "\n";
  		} 
  		$buffer .= "\n";
  	}
  	else
		  $buffer .= "No tests available.\n";
		  
		echo $buffer;
		
		echo debug :: parse_cli_console();
  }  
}

?>