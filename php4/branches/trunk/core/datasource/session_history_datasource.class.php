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
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/model/session_history_manager.class.php');

class session_history_datasource extends datasource
{	
	function & get_dataset(&$counter, $params)
	{
		$items = session_history_manager :: fetch();

		return new array_dataset($items);
	}
}
?>