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
require_once(LIMB_DIR . '/core/datasource/fetch_sub_branch_datasource.class.php');

class simple_navigation_datasource extends fetch_sub_branch_datasource
{
	function & _fetch(&$counter, $params)
	{
		$result =& parent :: _fetch($counter, $params);
		$uri = new uri($_SERVER['PHP_SELF']);		


		foreach($result as $key => $data)
		{
			$nav_uri = new uri($data['url']);

	  	if ($uri->get_host() != $nav_uri->get_host())
	  		continue;
			
			if(is_integer($res = $uri->compare_path($nav_uri)))
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