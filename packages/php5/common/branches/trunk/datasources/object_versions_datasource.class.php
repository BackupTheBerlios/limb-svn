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
require_once(LIMB_DIR . '/class/datasources/datasource.interface.php');

class object_versions_datasource implements datasource
{
	public function get_dataset(&$counter, $params=array())
	{
    $request = Limb :: toolkit()->getRequest();

    $datasource = Limb :: toolkit()->createDatasource('requested_object_datasource');
    $datasource->set_request($request);

		$object_data = $datasource->fetch();
    
		if (!count($object_data))
			return new array_dataset(array());

		$db_table	= Limb :: toolkit()->createDBTable('sys_object_version');

		$arr = $db_table->get_list('object_id='. $object_data['id'], 'version DESC');

		$result = array();

		$users = $this->_get_users();

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
  
  protected function _get_users()
  {
    $datasource = Limb :: toolkit()->createDatasource('site_objects_branch_datasource');
    $datasource->set_path('/root/users');
    $datasource->set_site_object_class_name('user_object');
    $datasource->set_restrict_by_class();
    
		return $datasource->fetch();
  }
}


?>