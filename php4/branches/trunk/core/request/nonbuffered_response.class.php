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

class nonbuffered_response
{			
	function write($string)
	{
	  echo $string;
	}
	
	function commit()
	{
	}
	
	function is_empty()
	{
	  return true;
	}			
} 
?>