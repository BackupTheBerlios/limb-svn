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
require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');

class search_text_normalizer_factory
{
	function search_text_normalizer_factory()
	{
	}
			
	function & instance($class_name)
	{	
		$obj =&	instantiate_object($class_name, '/core/model/search/normalizers/');
		return $obj;
	}
	
}
?>