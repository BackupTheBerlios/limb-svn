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
require_once(LIMB_DIR . 'class/core/request/response.interface.php');

class nonbuffered_response implements response
{			
	public function write($string)
	{
	  echo $string;
	}
	
	public function commit()
	{
	}
	
	public function is_empty()
	{
	  return true;
	}			
} 
?>