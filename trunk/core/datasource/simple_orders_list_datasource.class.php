<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: last_objects_datasource.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/datasource/fetch_datasource.class.php');

class simple_orders_list_datasource extends fetch_datasource
{
	function simple_orders_list_datasource()
	{
		parent :: fetch_datasource();
	}

	function & _fetch(&$counter, $params)
	{
		if(!$result =& parent :: _fetch($counter, $params))
			return $result;

		$user_ids = complex_array :: get_column_values('user_id', $result);

		$fetcher =& fetcher :: instance();
		
		$params = array(
			'restrict_by_class' => false
		);
		
		$user_counter = 0;
		$users =& $fetcher->fetch_by_ids($user_ids, 'user_object', $user_counter, $params, 'fetch_by_ids');

		foreach($result as $key => $data)
		{
			$customer_data = $users[$data['user_id']];
			$result[$key]['user_name'] = $customer_data['name'];
			$result[$key]['user_lastname'] = $customer_data['lastname'];
			$result[$key]['user_secondname'] = $customer_data['secondname'];
			$result[$key]['user_email'] = $customer_data['email'];
			$result[$key]['user_phone'] = $customer_data['phone'];
		}	

		return $result;
	}
}


?>