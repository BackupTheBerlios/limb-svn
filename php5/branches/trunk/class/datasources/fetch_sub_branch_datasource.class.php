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
require_once(LIMB_DIR . '/class/core/datasource/datasource.interface.php');

class fetch_sub_branch_datasource implements datasource
{
	public function get_dataset(&$counter, $params)
	{
	  return new array_dataset($this->_fetch($counter, $params));
  }

	protected function _fetch(&$counter, $params)
	{
		return fetcher :: instance()->fetch_sub_branch($params['path'], $params['loader_class_name'], $counter, $params, $params['fetch_method']);
	}
}



?>