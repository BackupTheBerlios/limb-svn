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
require_once(LIMB_DIR . 'class/core/sys_param.class.php');

class sys_param_component extends component
{
	public function get_param($name, $type)
	{
		echo sys_param :: instance()->get_param($name, $type);
	}
} 

?>