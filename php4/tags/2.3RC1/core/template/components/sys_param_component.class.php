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


require_once(LIMB_DIR . '/core/model/sys_param.class.php');


class sys_param_component extends component
{

	function get_param($name, $type)
	{
		$sys_param =& sys_param :: instance();
		echo $sys_param->get_param($name, $type);
	}
	
} 

?>