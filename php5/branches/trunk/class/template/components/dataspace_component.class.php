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

/**
* The dataspace_component does nothing other than extend component but is
* required to build the runtime component heirarchy, being the root component
*/
class dataspace_component extends component 
{
	public function register_dataset($dataset)
	{
		$this->import($dataset->export());
	}
}
?>