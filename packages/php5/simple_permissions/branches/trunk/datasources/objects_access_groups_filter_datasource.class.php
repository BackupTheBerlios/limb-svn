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
require_once(LIMB_DIR . '/class/datasources/options_datasource.interface.php');

class objects_access_groups_filter_datasource implements options_datasource
{
	public function get_options_array()
	{
		$params['order'] = array('priority' => 'ASC');
		$user_groups = $this->_get_user_groups();

		$options_array = array();

		foreach($user_groups as $key => $user)
			$options_array[$key] = $user['title'];

		return $options_array;
	}

  protected function _get_user_groups()
  {
    $datasource = Limb :: toolkit()->createDatasource('site_objects_branch_datasource');
    $datasource->set_path('/root/user_groups');
    $datasource->set_site_object_class_name('user_group');
    $datasource->set_restrict_by_class();
    
		return $datasource->fetch();
  }
  
	public function get_default_option()
	{
		return null;
	}
}


?>