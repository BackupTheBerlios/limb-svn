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
require_once(dirname(__FILE__) . '/TestRunner.class.php');
require_once(dirname(__FILE__) . '/LimbCliReporter.class.php');
require_once ('Console/Getopt.php');

class CLITestRunner extends TestRunner
{
  function usage()
  {
    $usage = <<<EOD
Usage: ./runtests.php [OPTION]...
  -b, --browse=PATH       list available tests cases at specified node
  -t, --test=PATH         test specified test cases node
  -h, --help              display this help and exit
EOD;    echo $usage;
    exit(0);
  }

  function run(&$root_group)
  {
    $opt_browse_path = false;
    $opt_test_path = false;

    $argv = ConsoleGetopt::readPHPArgv();
    if (PEAR::isError($argv))
    {
      die('Fatal Error: ' . $argv->getMessage()) . "\n";
    }

    $short_opts = 'ht:b:';
    $long_opts = array('help', 'test=', 'browse=');
    $options = ConsoleGetopt::getopt($argv, $short_opts, $long_opts);
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
        case '--browse':
          $opt_browse_path = $option[1];
          break;
      }
    }

    if ($opt_browse_path)
      $this->browse($opt_browse_path, $root_group);

    if ($opt_test_path)
      $this->perform($opt_test_path, $root_group);

    if(!$opt_browse_path &&  !$opt_test_path)
      $this->browse('', $root_group);

    exit(0);
  }

  function &_getReporter()
  {
    return new LimbCLIReporter();
  }

  function _displayBeforePerform($path, &$root_group, &$current_group)
  {
    echo '';
  }

  function _displayAfterPerform($path, &$root_group, &$current_group)
  {
    echo '';
  }

  function _displayBrowse($path, &$root_group, &$current_group)
  {
    if (is_a($current_group, 'LimbGroupTest'))
      $group_tests = $current_group->getTestCasesHandles();
    else
      $group_tests = array();

    $buffer = "Available test cases in \n'=== " . $current_group->getLabel() . " ===' :\n";

    if (sizeof($group_tests))
    {
      foreach ($group_tests as $index => $group_test)
      {
        resolveHandle($group_test);

        $buffer .= $path . '/' . $index . ' ' . $group_test->getLabel() . "\n";
      }
      $buffer .= "\n";
    }
    else
      $buffer .= "No tests available.\n";

    echo $buffer;

    echo Debug :: parseCliConsole();
  }
}

?>