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
require_once(LIMB_DIR . 'class/datasources/fetch_datasource.class.php');

class fetch_sub_branch_datasource extends fetch_datasource
{	
	protected function _fetch(&$counter, $params)
	{
		return fetch_sub_branch($params['path'], $params['loader_class_name'], $counter, $params, $params['fetch_method']);
	}
}



?>