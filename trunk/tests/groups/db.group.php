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

require_once(LIMB_DIR . '/tests/cases/db/drivers/driver_test_manager.class.php');

class tests_db extends GroupTest 
{
	function tests_db() 
	{
	  $this->GroupTest('db tests');
	  
	  //driver_test_manager :: restore();
	  
	  //driver_test_manager :: add_driver_suite_cases($this);
	  
	  //$this->addTestFile(LIMB_DIR . '/tests/cases/db/criteria.test.php');
	  //$this->addTestFile(LIMB_DIR . '/tests/cases/db/sql_builder.test.php');
	  $this->addTestFile(LIMB_DIR . '/tests/cases/db/db_table.test.php');
	}
}
?>