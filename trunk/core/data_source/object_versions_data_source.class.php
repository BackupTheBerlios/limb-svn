<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: object_versions_data_source.class.php 460 2004-02-17 15:34:52Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class object_versions_data_source extends data_source
{
	function object_versions_data_source()
	{
		parent :: data_source();
	}

	function & get_data_set(&$counter, $params=array())
	{
		$object_data = fetch_mapped_by_url();
		
		if (!count($object_data))
			return new array_dataset(array());
			
		$db_table	=  & db_table_factory :: instance('sys_object_version');
		
		$arr = $db_table->get_list('object_id='. $object_data['id'], 'version DESC');
		
		$result = array();
		
		$users =& fetch_sub_branch('/root/users', 'user_object', $counter);
		
		foreach($arr as $data)
		{
			$record = $data;
			$user = '';

			if (count($users))
				foreach($users as $user_data)
				{
					if ($user_data['id'] == $data['creator_id'])
					{
						$user = $user_data;
						break;					
					}
				}
			
			if ($user)
			{
				$record['creator_identifier'] = $user['identifier'];
				$record['creator_email'] = $user['email'];
				$record['creator_name'] = $user['name'];
				$record['creator_lastname'] = isset($user['lastname']) ? $user['lastname'] : '';
			}	
			$result[]	= $record;
		}
		
		return new array_dataset($result);
	}
	
}


?>