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
require_once(LIMB_DIR . 'class/core/fetcher.class.php');
require_once(LIMB_DIR . 'class/datasources/datasource.interface.php');

class fetch_datasource implements datasource
{
	public function get_dataset(&$counter, $params=array())
	{
		return new array_dataset($this->_fetch($counter, $params));
	}

	protected function _fetch(&$counter, $params)
	{
		return fetcher :: instance()->fetch($params['loader_class_name'], $counter, $params, $params['fetch_method']);
	}
}



?>