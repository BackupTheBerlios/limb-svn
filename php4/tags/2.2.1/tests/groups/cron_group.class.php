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

class cron_group extends GroupTest 
{
	function cron_group() 
	{
	  $this->GroupTest('cron tests');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/cron');
	}
}
?>