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
class data_source
{
	function data_source()
	{
	}

	function & get_data_set(&$counter, $params=array())
	{
		$counter = 0;
		return new array_dataset(array());
	}
	
}


?>