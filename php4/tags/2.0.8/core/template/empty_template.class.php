<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: template.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
class empty_template
{
	function empty_template()
	{
	} 

	function &get_child($server_id)
	{
		return null;
	} 

	function display()
	{
		debug :: write_error('template is null', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 

?>