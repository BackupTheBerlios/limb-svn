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


class image_library_group extends GroupTest 
{
	function image_library_group() 
	{
	  $this->GroupTest('Image library');
		$this->addTestFile(LIMB_DIR . '/tests/cases/image/gd_library_test.class.php');
	}
}
?>