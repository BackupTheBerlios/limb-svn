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
require_once(LIMB_DIR . 'class/fetcher.class.php');
require_once(LIMB_DIR . 'class/datasource/datasource.class.php');

class fetch_sub_branch_datasource extends datasource
{
	function & get_dataset(&$counter, $params=array())
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