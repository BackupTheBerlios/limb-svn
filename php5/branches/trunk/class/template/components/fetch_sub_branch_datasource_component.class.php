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
require_once(LIMB_DIR . 'class/core/array_dataset.class.php');
require_once(LIMB_DIR . 'class/datasources/datasource_factory.class.php');
require_once(LIMB_DIR . 'class/template/components/datasource_component.class.php');

class fetch_sub_branch_datasource_component extends datasource_component
{
	protected function _get_datasource()
	{
		if (!isset($this->parameters['datasource_path']))
			$this->parameters['datasource_path'] = '/fetch_sub_branch_datasource';
			
		$ds = parent :: _get_datasource();

		if(!$ds instanceof fetch_sub_branch_datasource)
      throw new WactException('ot allowed type of datasource, should be inherited from fetch_sub_branch_datasource class',
                                array('datasource' => get_class($ds)));
		return $ds;
	}
						
	protected function _get_params_array()
	{
		$params = $this->parameters;
		
		if(!isset($params['path']) || !$params['path'])
		{
      $request = Limb :: toolkit()->getRequest();
			$object_arr = Limb :: toolkit()->getFetcher()->fetch_requested_object($request);
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