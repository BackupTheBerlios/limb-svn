<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: simple_navigation_data_source.class.php 92 2004-03-29 08:29:10Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/fetch_tree_data_source.class.php');

class tree_navigation_data_source extends fetch_tree_data_source
{
	function tree_navigation_data_source()
	{
		parent :: fetch_tree_data_source();
	}

	function & _fetch(&$counter, $params)
	{
		$result =& parent :: _fetch($counter, $params);
		$uri = new uri(PHP_SELF);		

		foreach($result as $key => $data)
		{
			if($uri->compare($data['url'], $url_rest, $query_match))
			{
				if($url_rest >= 0)
					$result[$key]['in_path'] = true;
				if($url_rest == 0)
					$result[$key]['selected'] = true;
			}
		}
		return $result;
	}

}


?>