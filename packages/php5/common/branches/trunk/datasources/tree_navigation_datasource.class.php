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
require_once(LIMB_DIR . 'class/datasources/fetch_tree_datasource.class.php');
require_once(LIMB_DIR . 'class/lib/http/uri.class.php');

class tree_navigation_datasource extends fetch_tree_datasource
{
	function _fetch(&$counter, $params)
	{
		$result = parent :: _fetch($counter, $params);
		$uri = new uri($_SERVER['PHP_SELF']);		

		foreach($result as $key => $data)
		{
			if(is_integer($res = $uri->compare_path(new uri($data['url']))))
			{
				if($res >= 0)
					$result[$key]['in_path'] = true;
				if($res == 0)
					$result[$key]['selected'] = true;
			}
		}
		
		return $result;
	}
}


?>