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

function is_a($object, $classname) 
{
	return ((strtolower($classname) == get_class($object))
		or (is_subclass_of($object, $classname)));
}

?>