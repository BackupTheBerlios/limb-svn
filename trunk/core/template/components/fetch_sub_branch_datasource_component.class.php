<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/


require_once(LIMB_DIR . 'core/lib/util/array_dataset.class.php');
require_once(LIMB_DIR . 'core/datasource/datasource_factory.class.php');
require_once(LIMB_DIR . 'core/template/components/datasource_component.class.php');

class fetch_sub_branch_datasource_component extends datasource_component
{
	
	function & _get_datasource()
	{
		if (!isset($this->parameters['datasource_path']))
			$this->parameters['datasource_path'] = '/fetch_sub_branch_datasource';
			
		$ds =& parent :: _get_datasource();
		
		return $ds;
	}
						
	function _get_params_array()
	{
		$params = $this->parameters;
		
		if(!isset($params['path']) || !$params['path'])
			$params['path'] = $_SERVER['PHP_SELF'];
			
		if (!isset($params['loader_class_name']))
			$params['loader_class_name'] = 'site_object';

		if (!isset($params['fetch_method']))
			$params['fetch_method'] = 'fetch_by_ids';
		
		return $params;
	}
}

?>