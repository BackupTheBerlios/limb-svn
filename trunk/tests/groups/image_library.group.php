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


class tests_image_library extends GroupTest 
{
	function tests_image_library() 
	{
	  $this->GroupTest('Image library');
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/image');
	}
}
?>