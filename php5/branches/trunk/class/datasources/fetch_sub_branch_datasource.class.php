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

class fetch_sub_branch_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
	  return new array_dataset($this->_fetch($counter, $params));
  }

	protected function _fetch(&$counter, $params)
	{
    if(!isset($params['fetch_method']))
      return LimbToolsBox :: getToolkit()->getFetcher()->fetch_sub_branch($params['path'], $params['loader_class_name'], $counter, $params);
    else
      return LimbToolsBox :: getToolkit()->getFetcher()->fetch_sub_branch($params['path'], $params['loader_class_name'], $counter, $params, $params['fetch_method']);
	}
}



?>