<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: member_subscribe_params_data_source.class.php 239 2004-02-29 19:00:20Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');

class member_subscribe_params_datasource extends datasource
{
	function member_subscribe_params_datasource()
	{
		parent :: datasource();
	}

	function & get_dataset(& $counter, $params = array())
	{
		$themes =& fetch_sub_branch('/root/subscribe', 'subscribe_theme', $counter, $params);
		
		if (!count($themes))
			return new array_dataset(array());
			
		foreach($themes as $id => $theme_data)
		{
			$result[$theme_data['id']] = $theme_data;
			$result[$theme_data['id']]['selector_name'] = 'subscribe[' . $theme_data['id'] . ']';
		}	
		
		return new array_dataset($result);
	}
}


?>