<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: fetch_sub_branch_data_source.class.php 420 2004-02-09 16:40:33Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class fetch_sub_branch_data_source extends data_source
{
	function fetch_sub_branch_data_source()
	{
	}

	function & get_data_set(&$counter, $params=array())
	{
		$arr =& $this->_fetch($counter, $params);
		
		return new array_dataset($arr);
	}
	
	function & _fetch(&$counter, $params)
	{
		$arr =& fetch_sub_branch($params['path'], $params['loader_class_name'], $counter, $params, $params['fetch_method']);
		
		return $arr;
	}
}



?>