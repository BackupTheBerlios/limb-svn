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
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/model/links_manager.class.php');

class links_groups_datasource extends datasource
{
	function & get_dataset(&$counter, $params=array())
	{
		$counter = 0;
		
		$links_manager = new links_manager();
		$groups = $links_manager->fetch_groups();
		
		return new array_dataset($groups);
	}		
}


?>
