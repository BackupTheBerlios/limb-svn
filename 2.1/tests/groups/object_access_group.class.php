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


class object_access_group extends GroupTest 
{
	function object_access_group() 
	{
	  $this->GroupTest('object access tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/access');
	}
}
?>