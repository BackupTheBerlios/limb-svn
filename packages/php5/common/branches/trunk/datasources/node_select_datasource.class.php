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
require_once(LIMB_DIR . '/class/datasources/fetch_sub_branch_datasource.class.php');

class node_select_datasource extends fetch_sub_branch_datasource
{
	function get_dataset(&$counter, $params = array())
	{
		$params['depth'] = 1;

		if(Limb :: toolkit()->getRequest()->get('only_parents') == 'false')
			$params['only_parents'] = false;
		else
			$params['only_parents'] = true;

		$params['restrict_by_class'] = false;
		$params['path'] = $this->_process_path();

		return parent :: get_dataset($counter, $params);
	}

	function _process_path()
	{
		$default_path = '/root/';

		if(!$path = Limb :: toolkit()->getRequest()->get('path'))
			return $default_path;

		if(strpos($path, '?') !== false)
		{
			if(!$path = Limb :: toolkit()->getTree()->get_node_by_path($path))
				return $default_path;
		}
		return $path;
	}

}



?>