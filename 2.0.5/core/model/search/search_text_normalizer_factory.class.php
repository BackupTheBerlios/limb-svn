<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: site_object_factory.class.php 456 2004-02-16 18:52:50Z server $
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