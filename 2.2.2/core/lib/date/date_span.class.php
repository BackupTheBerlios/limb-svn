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

class date_span
{
	var $day = 0;
	var $hour = 0;
	var $minute = 0;
	var $second = 0;

	function date_span($seconds)
	{
		$this->second = $seconds;
		$this->day = intval($this->seconds / 86400);
		$this->second -= $this->day * 86400;
		$this->hour = intval($this->second / 3600);
		$this->second -= $this->hour * 3600;
		$this->minute = intval($this->second / 60);
		$this->second -= $this->minute * 60;
	} 
}

?> 