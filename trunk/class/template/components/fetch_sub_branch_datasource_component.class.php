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
require_once(LIMB_DIR . 'class/core/array_dataset.class.php');
require_once(LIMB_DIR . 'class/datasource/datasource_factory.class.php');
require_once(LIMB_DIR . 'class/template/components/datasource_component.class.php');

class fetch_sub_branch_datasource_component extends datasource_component
{
	function & _get_datasource()
	{
		if (!isset($this->parameters['datasource_path']))
			$this->parameters['datasource_path'] = '/fetch_sub_branch_datasource';
			
		$ds =& parent :: _get_datasource();
		
		if(!is_a($ds, 'fetch_sub_branch_datasource'))
			error('not allowed type of datasource, should be inherited from fetch_sub_branch_datasource class',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
			array('datasource' => get_class($ds)));

		return $ds;
	}
						
	function _get_params_array()
	{
		$params = $this->parameters;
		
		if(!isset($params['path']) || !$params['path'])
		{
			$object_arr =& fetch_requested_object();
			$params['path'] = $object_arr['path'];
		}				
		
		if (!isset($params['loader_class_name']))
			$params['loader_class_name'] = 'site_object';

		if (!isset($params['fetch_method']))
			$params['fetch_method'] = 'fetch_by_ids';
		
		return $params;
	}
}

?>