<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: last_objects_data_source.class.php 241 2004-03-04 10:31:50Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/fetch_data_source.class.php');

define ('DEFAULT_RANDOM_LIMIT', 3);

class random_objects_data_source extends fetch_data_source
{
	function random_objects_data_source()
	{
		parent :: fetch_data_source();
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