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