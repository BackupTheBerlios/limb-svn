<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class image_library_group extends LimbGroupTest 
{
	function image_library_group() 
	{
	  $this->LimbGroupTest('image library tests');
	}
	
	function getTestCasesHandles()
	{
 		return array(LIMB_DIR . '/tests/cases/image/gd_library_test');
	}	
}
?>