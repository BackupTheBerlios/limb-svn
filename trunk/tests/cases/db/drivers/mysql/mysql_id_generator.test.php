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
require_once(LIMB_DIR . '/tests/cases/db/id_generator.test.php');

class test_mysql_id_generator extends test_id_generator
{
	/** Ensures that drivers are implementing the correct Id Method. */
	function test_get_method() 
	{
		$this->assertEqual(id_generator::AUTOINCREMENT(), $this->idgen->get_id_method(), 0, "MySQL Id method should be AUTOINCREMENT (but is not)");
	}
    
}