<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'class/datasources/datasource.interface.php');

class object_versions_datasource implements datasource
{
	public function get_dataset(&$counter, $params=array())
	{
    $request = LimbToolsBox :: getToolkit()->getRequest();
		$object_data = LimbToolsBox :: getToolkit()->getFetcher()->fetch_requested_object($request);

		if (!count($object_data))
			return new array_dataset(array());

		$db_table	= LimbToolsBox :: getToolkit()->createDBTable('sys_object_version');

		$arr = $db_table->get_list('object_id='. $object_data['id'], 'version DESC');

		$result = array();

		$users = LimbToolsBox :: getToolkit()->getFetcher()->fetch_sub_branch('/root/users', 'user_object', $counter);

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