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


foreach($GLOBALS['tests'] as $test_name)
{
	require_once($test_name . '.php');
}

class tests_all extends GroupTest 
{
  function tests_all() 
  {
    $this->GroupTest('all tests');
    
		foreach($GLOBALS['tests'] as $test_name)
		{
			$this->addTestCase(new $test_name());
		}
  }
}

  
?>