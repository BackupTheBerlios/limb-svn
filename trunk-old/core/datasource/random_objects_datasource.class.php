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
require_once(LIMB_DIR . 'core/datasource/fetch_datasource.class.php');

define ('DEFAULT_RANDOM_LIMIT', 3);

class random_objects_datasource extends fetch_datasource
{
	function random_objects_datasource()
	{
		parent :: fetch_datasource();
	}

	function & _fetch(&$counter, $params)
	{
		$limit = DEFAULT_RANDOM_LIMIT;
		
		if (isset($params['limit']))
		{
			$limit = $params['limit'];
			unset($params['limit']);
		}
		
		if(!$all_objects =& parent :: _fetch($counter, $params))
			return array();
					
		$result = array();
		
		if ($limit >= count($all_objects))
			$limit = count($all_objects);
		
		$max_index = count($all_objects) - 1;
		$indexes = array_keys($all_objects);
		
		while(count($result) < $limit)
		{
			$index = mt_rand(0, $max_index);
			if (!isset($result[$index]))
				$result[$index] = $all_objects[$indexes[$index]];
		}
			
		return $result;
	}
}


?>